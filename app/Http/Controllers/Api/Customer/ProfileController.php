<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is a customer
        if (!$user->isCustomer()) {
            return response()->json([
                'message' => 'Only customers can update their profile'
            ], Response::HTTP_FORBIDDEN);
        }
        
        $userData = $request->only([
            'username', 'email', 'first_name', 'last_name', 'phone', 
            'address', 'city', 'country'
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $userData['profile'] = $path;

            // Delete old profile image if exists
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }
        }

        $user->fill($userData);
        $user->save();

        return new UserResource($user);
    }
    
    /**
     * Change customer password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        // Log password change request
        Log::info('Password change requested via ProfileController');

        // Validate the request
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        
        // Ensure user is a customer
        if (!$user->isCustomer()) {
            return response()->json([
                'message' => 'Only customers can change their password through this endpoint'
            ], Response::HTTP_FORBIDDEN);
        }

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ], Response::HTTP_OK);
    }

    public function updateProfileImage(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is a customer
        if (!$user->isCustomer()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            // Get raw request content and content type
            $rawContent = file_get_contents('php://input');
            $contentType = $request->header('Content-Type', '');
            $isMultipart = strpos($contentType, 'multipart/form-data') !== false;
            
            // Handle image upload
            $hasImage = false;
            $imagePath = null;
            
            // Method 1: Standard Laravel method
            if ($request->hasFile('profile_image')) {
                $hasImage = true;
                $file = $request->file('profile_image');
                
                if ($file->isValid()) {
                    $imagePath = $file->store('profile_images', 'public');
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
                        if ((strpos($part, 'name="profile_image"') !== false || 
                            strpos($part, 'name=profile_image') !== false) && 
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
                                            $filename = 'profile_images/' . uniqid() . '.' . $extension;
                                            Storage::disk('public')->put($filename, file_get_contents($tempFile));
                                            $imagePath = $filename;
                                        }
                                        
                                        // Clean up
                                        @unlink($tempFile);
                                    } catch (\Exception $e) {
                                        // Log the error but continue processing
                                        Log::error('Error processing image: ' . $e->getMessage());
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }
            
            // If no image was found or processed successfully
            if (!$hasImage || !$imagePath) {
                return response()->json([
                    'message' => 'No valid profile image provided'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Delete old profile image if it exists
            if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                Storage::disk('public')->delete($user->profile);
            }
            
            // Update user profile field
            $user->profile = $imagePath;
            $user->save();
            
            return response()->json([
                'message' => 'Profile image updated successfully'
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            Log::error('Profile image update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update profile image'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}