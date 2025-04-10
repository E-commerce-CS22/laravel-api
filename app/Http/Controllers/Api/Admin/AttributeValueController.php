<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of attribute values for a specific attribute
     *
     * @param int $attributeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($attributeId)
    {
        try {
            $attribute = Attribute::findOrFail($attributeId);
            $values = $attribute->values->makeHidden(['created_at', 'updated_at', 'attribute_id']);
            
            return response()->json([
            'success' => true,
            'data' => $values
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attribute values',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified attribute value
     *
     * @param int $attributeId
     * @param int $valueId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($attributeId, $valueId)
    {
        try {
            $attribute = Attribute::findOrFail($attributeId);
            $value = $attribute->values()->findOrFail($valueId);
            
            return response()->json([
                'success' => true,
                'data' => $value
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute value not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a newly created attribute value
     *
     * @param \Illuminate\Http\Request $request
     * @param int $attributeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $attributeId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $attribute = Attribute::findOrFail($attributeId);
            
            $data = $request->all();
            $data['attribute_id'] = $attributeId;
            
            $value = AttributeValue::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Attribute value created successfully',
                'data' => $value
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create attribute value',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified attribute value
     *
     * @param \Illuminate\Http\Request $request
     * @param int $attributeId
     * @param int $valueId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $attributeId, $valueId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $attribute = Attribute::findOrFail($attributeId);
            $value = $attribute->values()->findOrFail($valueId);
            
            $value->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Attribute value updated successfully',
                'data' => $value
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attribute value',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified attribute value
     *
     * @param int $attributeId
     * @param int $valueId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($attributeId, $valueId)
    {
        try {
            $attribute = Attribute::findOrFail($attributeId);
            $value = $attribute->values()->findOrFail($valueId);
            
            // Check if attribute value is used by any product variants
            if ($value->productVariants()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete attribute value. It is being used by product variants.'
                ], 400);
            }
            
            $value->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Attribute value deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attribute value',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
