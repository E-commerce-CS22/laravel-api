<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SlideController extends Controller
{
    public function index()
    {
        $slides = Slide::active()->orderBy('order')->get();
        
        return response()->json([
            'message' => 'Slides retrieved successfully',
            'data' => $slides->map(function ($slide) {
                return [
                    'id' => $slide->id,
                    'order' => $slide->order,
                    'image' => Storage::url($slide->image)
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $image = $request->file('image');
        $path = $image->store('slides', 'public');

        $slide = Slide::create([
            'order' => $request->order,
            'image' => $path,
            'status' => $request->input('status', 'inactive')
        ]);

        return response()->json(['message' => 'Slide created successfully', 'data' => $slide], 201);
    }

    public function update(Request $request, Slide $slide)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order' => 'sometimes|required|integer|min:0',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = [];

        if ($request->has('order')) {
            $data['order'] = $request->order;
        }

        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }
            
            $image = $request->file('image');
            $data['image'] = $image->store('slides', 'public');
        }

        $slide->update($data);

        return response()->json(['message' => 'Slide updated successfully', 'data' => $slide]);
    }

    public function destroy(Slide $slide)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($slide->image) {
            Storage::disk('public')->delete($slide->image);
        }

        $slide->delete();

        return response()->json(['message' => 'Slide deleted successfully']);
    }
}