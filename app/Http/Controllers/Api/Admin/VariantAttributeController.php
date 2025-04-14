<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VariantAttributeController extends Controller
{
    /**
     * Link an attribute value to a product variant
     *
     * @param Request $request
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkAttributeValue(Request $request, $variantId)
    {
        // Check if user is admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'attribute_id' => 'required|exists:attributes,id',
            'attribute_value_id' => 'required|exists:attribute_values,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Find the variant
            $variant = ProductVariant::findOrFail($variantId);
            
            // Check if attribute value belongs to the specified attribute
            $attributeValue = AttributeValue::where('id', $request->attribute_value_id)
                ->where('attribute_id', $request->attribute_id)
                ->first();
                
            if (!$attributeValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'The attribute value does not belong to the specified attribute'
                ], 400);
            }
            
            // Check if this association already exists
            $existingAssociation = DB::table('attribute_product_variant')
                ->where('product_variant_id', $variantId)
                ->where('attribute_id', $request->attribute_id)
                ->first();
                
            if ($existingAssociation) {
                // Update existing association
                DB::table('attribute_product_variant')
                    ->where('product_variant_id', $variantId)
                    ->where('attribute_id', $request->attribute_id)
                    ->update([
                        'attribute_value_id' => $request->attribute_value_id,
                        'updated_at' => now()
                    ]);
                    
                $message = 'Attribute value updated for variant successfully';
            } else {
                // Create new association
                DB::table('attribute_product_variant')->insert([
                    'product_variant_id' => $variantId,
                    'attribute_id' => $request->attribute_id,
                    'attribute_value_id' => $request->attribute_value_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $message = 'Attribute value linked to variant successfully';
            }
            
            // Get the updated variant with attributes
            $updatedVariant = ProductVariant::with(['attributes', 'attributeValues'])->findOrFail($variantId);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'variant' => $updatedVariant,
                    'product_id' => $updatedVariant->product_id
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to link attribute value to variant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove an attribute from a product variant
     *
     * @param int $variantId
     * @param int $attributeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlinkAttribute($variantId, $attributeId)
    {
        // Check if user is admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            // Find the variant
            $variant = ProductVariant::findOrFail($variantId);
            
            // Check if the attribute exists
            $attribute = Attribute::findOrFail($attributeId);
            
            // Remove the association
            $deleted = DB::table('attribute_product_variant')
                ->where('product_variant_id', $variantId)
                ->where('attribute_id', $attributeId)
                ->delete();
                
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute not found for this variant'
                ], 404);
            }
            
            // Get the updated variant with attributes
            $updatedVariant = ProductVariant::with(['attributes', 'attributeValues'])->findOrFail($variantId);
            
            return response()->json([
                'success' => true,
                'message' => 'Attribute unlinked from variant successfully',
                'data' => [
                    'variant' => $updatedVariant,
                    'product_id' => $updatedVariant->product_id
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink attribute from variant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all attributes and their values for a specific variant
     *
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVariantAttributes($variantId)
    {
        try {
            // Find the variant
            $variant = ProductVariant::with(['attributes', 'attributeValues'])->findOrFail($variantId);
            
            // Format the response data
            $attributes = [];
            foreach ($variant->attributes as $attribute) {
                $attributeValue = $variant->attributeValues()
                    ->wherePivot('attribute_id', $attribute->id)
                    ->first();
                    
                $attributes[] = [
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                    'attribute_value_id' => $attributeValue ? $attributeValue->id : null,
                    'attribute_value_name' => $attributeValue ? $attributeValue->name : null
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'variant_title' => $variant->variant_title,
                    'attributes' => $attributes
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve variant attributes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all variants for a specific product with their attributes
     *
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductVariantsWithAttributes($productId)
    {
        try {
            // Find the product
            $product = Product::findOrFail($productId);
            
            // Check if it's a parent product
            if (!$product->is_parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only parent products can have variants'
                ], 400);
            }
            
            // Get all variants with their attributes
            $variants = ProductVariant::where('product_id', $productId)
                ->with(['attributes', 'attributeValues'])
                ->get();
                
            // Format the response data
            $formattedVariants = [];
            foreach ($variants as $variant) {
                $attributes = [];
                foreach ($variant->attributes as $attribute) {
                    $attributeValue = $variant->attributeValues()
                        ->wherePivot('attribute_id', $attribute->id)
                        ->first();
                        
                    $attributes[] = [
                        'attribute_id' => $attribute->id,
                        'attribute_name' => $attribute->name,
                        'attribute_value_id' => $attributeValue ? $attributeValue->id : null,
                        'attribute_value_name' => $attributeValue ? $attributeValue->name : null
                    ];
                }
                
                $formattedVariants[] = [
                    'variant_id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                    'is_default' => $variant->is_default,
                    'variant_title' => $variant->variant_title,
                    'attributes' => $attributes
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variants' => $formattedVariants
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product variants with attributes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
