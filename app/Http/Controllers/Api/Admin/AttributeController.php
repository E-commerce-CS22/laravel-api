<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    /**
     * Display a listing of all attributes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $attributes = Attribute::with('values')->get()->makeHidden(['created_at', 'updated_at']);
            
            return response()->json([
                'success' => true,
                'data' => $attributes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attributes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified attribute
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $attribute = Attribute::with('values')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $attribute
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a newly created attribute
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:attributes,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $attribute = Attribute::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Attribute created successfully',
                'data' => $attribute
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create attribute',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified attribute
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:attributes,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully',
                'data' => $attribute
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attribute',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified attribute
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            
            // Check if attribute is used by any product variants
            if ($attribute->variants()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete attribute. It is being used by product variants.'
                ], 400);
            }
            
            // Delete all associated attribute values
            $attribute->values()->delete();
            
            // Delete the attribute
            $attribute->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attribute',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
