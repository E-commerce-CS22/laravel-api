<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class CategoryManagementController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    private function isAdmin(Request $request)
    {
        return $request->user() && $request->user()->admin()->exists();
    }

    public function index(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $categories = $this->categoryService->getAllCategories();
            return CategoryResource::collection($categories);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryManagementController@index: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'An error occurred while fetching categories',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            // Get raw request content and content type
            $rawContent = file_get_contents('php://input');
            $contentType = $request->header('Content-Type', '');
            $isMultipart = strpos($contentType, 'multipart/form-data') !== false;
            
            // Initialize data array for category creation
            $data = [];
            
            // For JSON requests, get data from the JSON body
            if ($request->isJson()) {
                $jsonData = $request->json()->all();
                
                // Validate required fields
                if (!isset($jsonData['name'])) {
                    return response()->json([
                        'message' => 'The name field is required'
                    ], Response::HTTP_BAD_REQUEST);
                }
                
                $data['name'] = $jsonData['name'];
                if (isset($jsonData['slug'])) $data['slug'] = $jsonData['slug'];
                if (isset($jsonData['description'])) $data['description'] = $jsonData['description'];
            } 
            // For multipart form-data requests
            else if ($isMultipart) {
                // Try standard Laravel methods first
                $name = $request->input('name');
                $slug = $request->input('slug');
                $description = $request->input('description');
                
                // If standard methods fail, parse the raw content
                if (empty($name) && !empty($rawContent)) {
                    // Extract form fields from raw content
                    if (preg_match('/name="name"[\r\n]+[\r\n]+(.*?)[\r\n]+--/s', $rawContent, $matches)) {
                        $name = trim($matches[1]);
                    }
                }
                
                if (empty($slug) && !empty($rawContent)) {
                    if (preg_match('/name="slug"[\r\n]+[\r\n]+(.*?)[\r\n]+--/s', $rawContent, $matches)) {
                        $slug = trim($matches[1]);
                    }
                }
                
                if (empty($description) && !empty($rawContent)) {
                    if (preg_match('/name="description"[\r\n]+[\r\n]+(.*?)[\r\n]+--/s', $rawContent, $matches)) {
                        $description = trim($matches[1]);
                    }
                }
                
                // Validate required fields
                if (empty($name)) {
                    return response()->json([
                        'message' => 'The name field is required'
                    ], Response::HTTP_BAD_REQUEST);
                }
                
                // Add extracted data to the data array
                $data['name'] = $name;
                if (!empty($slug)) $data['slug'] = $slug;
                if (!empty($description)) $data['description'] = $description;
            } else {
                // For other request types, check if we have a name
                if (!$request->has('name')) {
                    return response()->json([
                        'message' => 'The name field is required'
                    ], Response::HTTP_BAD_REQUEST);
                }
                
                // Get text fields from the request
                $data['name'] = $request->input('name');
                if ($request->has('slug')) $data['slug'] = $request->input('slug');
                if ($request->has('description')) $data['description'] = $request->input('description');
            }
            
            // Handle image upload
            $hasImage = false;
            
            // Method 1: Standard Laravel method
            if ($request->hasFile('image')) {
                $hasImage = true;
                $file = $request->file('image');
                
                if ($file->isValid()) {
                    $data['image'] = $file->store('categories', 'public');
                }
            }
            // Method 2: Check for image in raw content
            else if ($isMultipart && !empty($rawContent)) {
                // Look for image content in the raw data
                $boundary = '';
                if (preg_match('/boundary=(.*)$/', $contentType, $matches)) {
                    $boundary = $matches[1];
                }
                
                if (!empty($boundary)) {
                    // Find the image part in the multipart data
                    $parts = explode('--' . $boundary, $rawContent);
                    foreach ($parts as $part) {
                        // Check for image part using various patterns
                        if ((strpos($part, 'name="image"') !== false || 
                            strpos($part, 'name=image') !== false) && 
                            strpos($part, 'Content-Type: image/') !== false) {
                            
                            $hasImage = true;
                            
                            // Extract content type
                            if (preg_match('/Content-Type: (image\/[^\r\n]+)/i', $part, $matches)) {
                                $imageContentType = $matches[1];
                                
                                // Extract image data - everything after the double newline
                                $imageDataPos = strpos($part, "\r\n\r\n");
                                if ($imageDataPos !== false) {
                                    $imageData = substr($part, $imageDataPos + 4);
                                    // Remove the last \r\n if present
                                    $imageData = rtrim($imageData, "\r\n");
                                    
                                    // Determine file extension from content type
                                    $extension = '';
                                    if (strpos($imageContentType, 'png') !== false) {
                                        $extension = 'png';
                                    } else if (strpos($imageContentType, 'gif') !== false) {
                                        $extension = 'gif';
                                    } else if (strpos($imageContentType, 'jpeg') !== false || strpos($imageContentType, 'jpg') !== false) {
                                        $extension = 'jpg';
                                    }
                                    
                                    // Default to jpg if no extension was determined
                                    if (empty($extension)) {
                                        $extension = 'jpg';
                                    }
                                    
                                    try {
                                        // Create a temporary file and store the image
                                        $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                                        file_put_contents($tempFile, $imageData);
                                        
                                        // Check if the file is a valid image
                                        $isValidImage = @getimagesize($tempFile) !== false;
                                        
                                        if ($isValidImage) {
                                            // Store the image
                                            $filename = 'categories/' . uniqid() . '.' . $extension;
                                            Storage::disk('public')->put($filename, file_get_contents($tempFile));
                                            $data['image'] = $filename;
                                        }
                                        
                                        // Clean up
                                        @unlink($tempFile);
                                    } catch (\Exception $e) {
                                        // Log the error but continue processing
                                        \Log::error('Error processing image: ' . $e->getMessage());
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // Create the category
            $category = $this->categoryService->createCategory($data);

            // Check for image pattern in raw content for debugging
            $hasImagePattern = strpos($rawContent, 'name="image"') !== false;
            $hasImageContentType = strpos($rawContent, 'Content-Type: image/') !== false;
            
            // Extract boundary for debugging
            $boundary = '';
            if (preg_match('/boundary=(.*)$/', $contentType, $matches)) {
                $boundary = $matches[1];
            }
            
            // Count parts for debugging
            $partsCount = 0;
            $imagePartFound = false;
            $imagePartSample = '';
            if (!empty($boundary)) {
                $parts = explode('--' . $boundary, $rawContent);
                $partsCount = count($parts);
                
                // Look for image part
                foreach ($parts as $part) {
                    if ((strpos($part, 'name="image"') !== false || 
                        strpos($part, 'name=image') !== false) && 
                        strpos($part, 'Content-Type: image/') !== false) {
                        $imagePartFound = true;
                        // Only take the headers part to avoid binary data
                        $headerEndPos = strpos($part, "\r\n\r\n");
                        if ($headerEndPos !== false) {
                            $imagePartSample = substr($part, 0, $headerEndPos);
                        } else {
                            $imagePartSample = substr($part, 0, 100) . '...';
                        }
                        break;
                    }
                }
            }
            
            // Make sure raw content sample doesn't include binary data
            $rawContentSample = '';
            $firstBoundaryPos = strpos($rawContent, '--' . $boundary);
            if ($firstBoundaryPos !== false) {
                $rawContentSample = substr($rawContent, 0, min(500, $firstBoundaryPos + 100)) . '...';
            } else {
                $rawContentSample = substr($rawContent, 0, 100) . '...';
            }
            
            // Ensure all debug data is properly encoded
            $debugInfo = [
                'created_fields' => $data,
                'has_image' => isset($data['image']) ? 'yes' : 'no',
                'request_info' => [
                    'content_type' => $contentType,
                    'is_multipart' => $isMultipart ? 'yes' : 'no',
                    'is_json' => $request->isJson() ? 'yes' : 'no',
                    'has_file' => $request->hasFile('image') ? 'yes' : 'no',
                    'raw_content_length' => strlen($rawContent),
                    'extracted_name' => $data['name'] ?? null,
                    'extracted_slug' => $data['slug'] ?? null,
                    'extracted_description' => $data['description'] ?? null,
                    'has_image_detected' => $hasImage ? 'yes' : 'no',
                    'has_image_pattern' => $hasImagePattern ? 'yes' : 'no',
                    'has_image_content_type' => $hasImageContentType ? 'yes' : 'no',
                    'boundary' => $boundary,
                    'parts_count' => $partsCount,
                    'image_part_found' => $imagePartFound ? 'yes' : 'no',
                    'image_part_sample' => $imagePartFound ? $imagePartSample : '',
                    'raw_content_sample' => $rawContentSample
                ]
            ];
            
            // Make sure we can encode the debug info to JSON
            try {
                json_encode($debugInfo);
            } catch (\Exception $e) {
                // If we can't encode it, simplify the debug info
                $debugInfo = [
                    'error' => 'Could not encode debug info: ' . $e->getMessage(),
                    'created_fields' => $data,
                    'has_image' => isset($data['image']) ? 'yes' : 'no'
                ];
            }
            
            return response()->json([
                'message' => 'Category created successfully',
                'data' => new CategoryResource($category),
                'debug_info' => $debugInfo
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating category',
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Category $category)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            // Get raw request content and content type
            $rawContent = file_get_contents('php://input');
            $contentType = $request->header('Content-Type', '');
            $isMultipart = strpos($contentType, 'multipart/form-data') !== false;
            
            // Initialize data array for category update
            $data = [];
            
            // For JSON requests, get data from the JSON body
            if ($request->isJson()) {
                $jsonData = $request->json()->all();
                if (isset($jsonData['name'])) $data['name'] = $jsonData['name'];
                if (isset($jsonData['slug'])) $data['slug'] = $jsonData['slug'];
                if (isset($jsonData['description'])) $data['description'] = $jsonData['description'];
            } 
            // For multipart form-data requests
            else if ($isMultipart) {
                // Try standard Laravel methods first
                $name = $request->input('name');
                $slug = $request->input('slug');
                $description = $request->input('description');
                
                // If standard methods fail, parse the raw content
                if (empty($name) && !empty($rawContent)) {
                    // Extract form fields from raw content
                    if (preg_match('/name="name"[\r\n]+[\r\n]+(.*?)[\r\n]+--/s', $rawContent, $matches)) {
                        $name = trim($matches[1]);
                    }
                }
                
                if (empty($slug) && !empty($rawContent)) {
                    if (preg_match('/name="slug"[\r\n]+[\r\n]+(.*?)[\r\n]+--/s', $rawContent, $matches)) {
                        $slug = trim($matches[1]);
                    }
                }
                
                if (empty($description) && !empty($rawContent)) {
                    if (preg_match('/name="description"[\r\n]+[\r\n]+(.*?)[\r\n]+--/s', $rawContent, $matches)) {
                        $description = trim($matches[1]);
                    }
                }
                
                // Add extracted data to the data array
                if (!empty($name)) $data['name'] = $name;
                if (!empty($slug)) $data['slug'] = $slug;
                if (!empty($description)) $data['description'] = $description;
            }
            
            // Handle image upload
            $hasImage = false;
            
            // Method 1: Standard Laravel method
            if ($request->hasFile('image')) {
                $hasImage = true;
                $file = $request->file('image');
                
                if ($file->isValid()) {
                    // Delete old image if exists
                    if ($category->image) {
                        Storage::disk('public')->delete($category->image);
                    }
                    
                    // Store new image
                    $data['image'] = $file->store('categories', 'public');
                }
            }
            // Method 2: Check for image in raw content
            else if ($isMultipart && !empty($rawContent)) {
                // Look for image content in the raw data
                $boundary = '';
                if (preg_match('/boundary=(.*)$/', $contentType, $matches)) {
                    $boundary = $matches[1];
                }
                
                if (!empty($boundary)) {
                    // Find the image part in the multipart data
                    $parts = explode('--' . $boundary, $rawContent);
                    foreach ($parts as $part) {
                        // Check for image part using various patterns
                        if ((strpos($part, 'name="image"') !== false || 
                            strpos($part, 'name=image') !== false) && 
                            strpos($part, 'Content-Type: image/') !== false) {
                            
                            $hasImage = true;
                            
                            // Extract content type
                            if (preg_match('/Content-Type: (image\/[^\r\n]+)/i', $part, $matches)) {
                                $imageContentType = $matches[1];
                                
                                // Extract image data - everything after the double newline
                                $imageDataPos = strpos($part, "\r\n\r\n");
                                if ($imageDataPos !== false) {
                                    $imageData = substr($part, $imageDataPos + 4);
                                    // Remove the last \r\n if present
                                    $imageData = rtrim($imageData, "\r\n");
                                    
                                    // Determine file extension from content type
                                    $extension = '';
                                    if (strpos($imageContentType, 'png') !== false) {
                                        $extension = 'png';
                                    } else if (strpos($imageContentType, 'gif') !== false) {
                                        $extension = 'gif';
                                    } else if (strpos($imageContentType, 'jpeg') !== false || strpos($imageContentType, 'jpg') !== false) {
                                        $extension = 'jpg';
                                    }
                                    
                                    // Default to jpg if no extension was determined
                                    if (empty($extension)) {
                                        $extension = 'jpg';
                                    }
                                    
                                    try {
                                        // Delete old image if exists
                                        if ($category->image) {
                                            Storage::disk('public')->delete($category->image);
                                        }
                                        
                                        // Create a temporary file and store the image
                                        $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                                        file_put_contents($tempFile, $imageData);
                                        
                                        // Check if the file is a valid image
                                        $isValidImage = @getimagesize($tempFile) !== false;
                                        
                                        if ($isValidImage) {
                                            // Store the image
                                            $filename = 'categories/' . uniqid() . '.' . $extension;
                                            Storage::disk('public')->put($filename, file_get_contents($tempFile));
                                            $data['image'] = $filename;
                                        }
                                        
                                        // Clean up
                                        @unlink($tempFile);
                                    } catch (\Exception $e) {
                                        // Log the error but continue processing
                                        \Log::error('Error processing image: ' . $e->getMessage());
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // Update the category with the new data
            if (!empty($data)) {
                $this->categoryService->updateCategory($category, $data);
                $category = $category->fresh();
            }
            
            // Check for image pattern in raw content for debugging
            $hasImagePattern = strpos($rawContent, 'name="image"') !== false;
            $hasImageContentType = strpos($rawContent, 'Content-Type: image/') !== false;
            
            // Extract boundary for debugging
            $boundary = '';
            if (preg_match('/boundary=(.*)$/', $contentType, $matches)) {
                $boundary = $matches[1];
            }
            
            // Count parts for debugging
            $partsCount = 0;
            $imagePartFound = false;
            $imagePartSample = '';
            if (!empty($boundary)) {
                $parts = explode('--' . $boundary, $rawContent);
                $partsCount = count($parts);
                
                // Look for image part
                foreach ($parts as $part) {
                    if ((strpos($part, 'name="image"') !== false || 
                        strpos($part, 'name=image') !== false) && 
                        strpos($part, 'Content-Type: image/') !== false) {
                        $imagePartFound = true;
                        // Only take the headers part to avoid binary data
                        $headerEndPos = strpos($part, "\r\n\r\n");
                        if ($headerEndPos !== false) {
                            $imagePartSample = substr($part, 0, $headerEndPos);
                        } else {
                            $imagePartSample = substr($part, 0, 100) . '...';
                        }
                        break;
                    }
                }
            }
            
            // Make sure raw content sample doesn't include binary data
            $rawContentSample = '';
            $firstBoundaryPos = strpos($rawContent, '--' . $boundary);
            if ($firstBoundaryPos !== false) {
                $rawContentSample = substr($rawContent, 0, min(500, $firstBoundaryPos + 100)) . '...';
            } else {
                $rawContentSample = substr($rawContent, 0, 100) . '...';
            }
            
            // Ensure all debug data is properly encoded
            $debugInfo = [
                'updated_fields' => $data,
                'has_image' => isset($data['image']) ? 'yes' : 'no',
                'request_info' => [
                    'content_type' => $contentType,
                    'is_multipart' => $isMultipart ? 'yes' : 'no',
                    'is_json' => $request->isJson() ? 'yes' : 'no',
                    'has_file' => $request->hasFile('image') ? 'yes' : 'no',
                    'raw_content_length' => strlen($rawContent),
                    'extracted_name' => $data['name'] ?? null,
                    'extracted_slug' => $data['slug'] ?? null,
                    'extracted_description' => $data['description'] ?? null,
                    'has_image_detected' => $hasImage ? 'yes' : 'no',
                    'has_image_pattern' => $hasImagePattern ? 'yes' : 'no',
                    'has_image_content_type' => $hasImageContentType ? 'yes' : 'no',
                    'boundary' => $boundary,
                    'parts_count' => $partsCount,
                    'image_part_found' => $imagePartFound ? 'yes' : 'no',
                    'image_part_sample' => $imagePartFound ? $imagePartSample : '',
                    'raw_content_sample' => $rawContentSample
                ]
            ];
            
            // Make sure we can encode the debug info to JSON
            try {
                json_encode($debugInfo);
            } catch (\Exception $e) {
                // If we can't encode it, simplify the debug info
                $debugInfo = [
                    'error' => 'Could not encode debug info: ' . $e->getMessage(),
                    'updated_fields' => $data,
                    'has_image' => isset($data['image']) ? 'yes' : 'no'
                ];
            }
            
            return response()->json([
                'message' => 'Category updated successfully',
                'data' => new CategoryResource($category),
                'debug_info' => $debugInfo
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating category',
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, Category $category)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $this->categoryService->deleteCategory($category);

            return response()->json([
                'message' => 'Category deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryManagementController@destroy: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, Category $category)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized. Only admins can access this area.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            return new CategoryResource($category);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching category details',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}