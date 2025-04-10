<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use App\Repositories\ProductImageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    protected $productRepository;
    protected $productVariantRepository;
    protected $productImageRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository = null,
        ProductImageRepository $productImageRepository = null
    ) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository ?? app(ProductVariantRepository::class);
        $this->productImageRepository = $productImageRepository ?? app(ProductImageRepository::class);
    }

    public function getAllProducts($perPage = null, $page = null)
    {
        return $this->productRepository->getAll($perPage, $page);
    }

    public function getParentProducts($perPage = null, $page = null)
    {
        return $this->productRepository->findWhere(['is_parent' => true], $perPage, $page);
    }

    public function createProduct($data)
    {
        DB::beginTransaction();
        
        try {
            // Determine if this is a parent product
            $isParent = isset($data['is_parent']) ? $data['is_parent'] : false;
            $data['is_parent'] = $isParent;
            
            // Create the product
            $product = $this->productRepository->create($data);
            
            // Handle categories if provided
            if (isset($data['categories']) && is_array($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }
            
            // Handle tags if provided
            if (isset($data['tags']) && is_array($data['tags'])) {
                $product->tags()->sync($data['tags']);
            }
            
            // Handle product images
            if (isset($data['images']) && is_array($data['images'])) {
                $this->handleProductImages($product, $data['images']);
            }
            
            // Handle variants if this is a parent product
            if ($isParent && isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $this->createProductVariant($product->id, $variantData);
                }
            }
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getProductById($id)
    {
        return $this->productRepository->findById($id);
    }

    public function updateProduct($id, $data)
    {
        DB::beginTransaction();
        
        try {
            $product = $this->productRepository->findById($id);
            
            // Update the product
            $product = $this->productRepository->update($id, $data);
            
            // Handle categories if provided
            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }
            
            // Handle tags if provided
            if (isset($data['tags'])) {
                $product->tags()->sync($data['tags']);
            }
            
            // Handle product images
            if (isset($data['images'])) {
                $this->handleProductImages($product, $data['images']);
            }
            
            // Handle variants if this is a parent product
            if ($product->is_parent && isset($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    if (isset($variantData['id'])) {
                        $this->updateProductVariant($variantData['id'], $variantData);
                    } else {
                        $this->createProductVariant($product->id, $variantData);
                    }
                }
            }
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteProduct($id)
    {
        DB::beginTransaction();
        
        try {
            $product = $this->productRepository->findById($id);
            
            // If this is a parent product, delete all child products
            if ($product->is_parent) {
                foreach ($product->children as $child) {
                    $this->deleteProduct($child->id);
                }
            }
            
            // Delete all variants
            foreach ($product->variants as $variant) {
                $this->productVariantRepository->delete($variant->id);
            }
            
            // Delete all images
            foreach ($product->images as $image) {
                // Delete the file from storage
                if (Storage::exists($image->image_path)) {
                    Storage::delete($image->image_path);
                }
                $this->productImageRepository->delete($image->id);
            }
            
            // Delete the product
            $product = $this->productRepository->delete($id);
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Apply discount to a product
     * 
     * @param int $id
     * @param array $discountData
     * @return \App\Models\Product
     */
    public function applyDiscount($id, array $discountData)
    {
        return $this->productRepository->update($id, $discountData);
    }
    
    /**
     * Remove discount from a product
     * 
     * @param int $id
     * @return \App\Models\Product
     */
    public function removeDiscount($id)
    {
        $discountData = [
            'discount_type' => null,
            'discount_value' => null,
            'discount_start_date' => null,
            'discount_end_date' => null
        ];
        
        return $this->productRepository->update($id, $discountData);
    }
    
    /**
     * Create a product variant
     * 
     * @param int $productId
     * @param array $data
     * @return \App\Models\ProductVariant
     */
    public function createProductVariant($productId, array $data)
    {
        DB::beginTransaction();
        
        try {
            // Add product_id to data
            $data['product_id'] = $productId;
            
            // Create the variant
            $variant = $this->productVariantRepository->create($data);
            
            // Handle attributes if provided
            if (isset($data['attributes']) && is_array($data['attributes'])) {
                $attributeData = [];
                foreach ($data['attributes'] as $attribute) {
                    $attributeData[$attribute['attribute_id']] = ['attribute_value_id' => $attribute['attribute_value_id']];
                }
                $variant->attributes()->sync($attributeData);
            }
            
            // Handle variant images
            if (isset($data['images']) && is_array($data['images'])) {
                $this->handleVariantImages($variant, $data['images']);
            }
            
            // If this is the default variant, make sure no other variants are default
            if (isset($data['is_default']) && $data['is_default']) {
                $this->productVariantRepository->updateWhereNot(
                    ['product_id' => $productId, 'id' => $variant->id],
                    ['is_default' => false]
                );
            }
            
            DB::commit();
            return $variant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update a product variant
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\ProductVariant
     */
    public function updateProductVariant($id, array $data)
    {
        DB::beginTransaction();
        
        try {
            $variant = $this->productVariantRepository->findById($id);
            
            // Update the variant
            $variant = $this->productVariantRepository->update($id, $data);
            
            // Handle attributes if provided
            if (isset($data['attributes']) && is_array($data['attributes'])) {
                $attributeData = [];
                foreach ($data['attributes'] as $attribute) {
                    $attributeData[$attribute['attribute_id']] = ['attribute_value_id' => $attribute['attribute_value_id']];
                }
                $variant->attributes()->sync($attributeData);
            }
            
            // Handle variant images
            if (isset($data['images']) && is_array($data['images'])) {
                $this->handleVariantImages($variant, $data['images']);
            }
            
            // If this is the default variant, make sure no other variants are default
            if (isset($data['is_default']) && $data['is_default']) {
                $this->productVariantRepository->updateWhereNot(
                    ['product_id' => $variant->product_id, 'id' => $variant->id],
                    ['is_default' => false]
                );
            }
            
            DB::commit();
            return $variant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete a product variant
     * 
     * @param int $id
     * @return \App\Models\ProductVariant
     */
    public function deleteProductVariant($id)
    {
        DB::beginTransaction();
        
        try {
            $variant = $this->productVariantRepository->findById($id);
            
            // Delete all images
            foreach ($variant->images as $image) {
                // Delete the file from storage
                if (Storage::exists($image->path)) {
                    Storage::delete($image->path);
                }
                $this->productImageRepository->delete($image->id);
            }
            
            // Delete the variant
            $variant = $this->productVariantRepository->delete($id);
            
            DB::commit();
            return $variant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Handle variant images
     * 
     * @param \App\Models\ProductVariant $variant
     * @param array $images
     * @return void
     */
    protected function handleVariantImages($variant, array $images)
    {
        $existingImageIds = [];
        
        foreach ($images as $imageData) {
            // If an ID is provided, update the existing image
            if (isset($imageData['id'])) {
                $existingImageIds[] = $imageData['id'];
                $this->productImageRepository->update($imageData['id'], [
                    'alt_text' => $imageData['alt_text'] ?? null,
                    'is_primary' => $imageData['is_primary'] ?? false,
                    'sort_order' => $imageData['sort_order'] ?? 0,
                    'image_type' => $imageData['image_type'] ?? 'gallery'
                ]);
            }
            // If a file is provided, create a new image
            elseif (isset($imageData['file']) && $imageData['file'] instanceof UploadedFile) {
                $path = $imageData['file']->store('product-images', 'public');
                
                $newImage = $this->productImageRepository->create([
                    'product_id' => null,
                    'product_variant_id' => $variant->id,
                    'path' => $path,
                    'alt_text' => $imageData['alt_text'] ?? null,
                    'is_primary' => $imageData['is_primary'] ?? false,
                    'sort_order' => $imageData['sort_order'] ?? 0,
                    'image_type' => $imageData['image_type'] ?? 'gallery'
                ]);
                
                $existingImageIds[] = $newImage->id;
            }
        }
        
        // Delete any images not included in the update
        foreach ($variant->images as $image) {
            if (!in_array($image->id, $existingImageIds)) {
                // Delete the file from storage
                if (Storage::exists($image->path)) {
                    Storage::delete($image->path);
                }
                $this->productImageRepository->delete($image->id);
            }
        }
    }
    
    /**
     * Handle product images
     * 
     * @param \App\Models\Product $product
     * @param array $images
     * @return void
     */
    protected function handleProductImages($product, $images)
    {
        // Handle image updates or deletions
        $existingImageIds = $product->images->pluck('id')->toArray();
        $updatedImageIds = [];
        
        foreach ($images as $imageData) {
            if (isset($imageData['id']) && in_array($imageData['id'], $existingImageIds)) {
                // Update existing image
                $this->productImageRepository->update($imageData['id'], $imageData);
                $updatedImageIds[] = $imageData['id'];
            } elseif (isset($imageData['file']) && $imageData['file'] instanceof UploadedFile) {
                // Upload new image
                $path = $this->uploadImage($imageData['file'], 'products/' . $product->id);
                
                $imageAttributes = [
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'alt_text' => $imageData['alt_text'] ?? $product->name,
                    'is_primary' => $imageData['is_primary'] ?? false,
                    'sort_order' => $imageData['sort_order'] ?? 0,
                    'image_type' => $imageData['image_type'] ?? 'gallery'
                ];
                
                $this->productImageRepository->create($imageAttributes);
            }
        }
        
        // Delete images that were not updated
        $imagesToDelete = array_diff($existingImageIds, $updatedImageIds);
        foreach ($imagesToDelete as $imageId) {
            $image = $this->productImageRepository->findById($imageId);
            
            // Delete the file from storage
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
            }
            
            $this->productImageRepository->delete($imageId);
        }
    }
    
    /**
     * Upload an image
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    protected function uploadImage($file, $path)
    {
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($path, $filename, 'public');
        
        return $filePath;
    }
}
