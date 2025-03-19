<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Exception;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        $tags = $this->tagService->getAllTags();
        return TagResource::collection($tags);
    }

    public function show($id)
    {
        $tag = $this->tagService->getTagById($id);
        if (!$tag) {
            return response()->json(['error' => 'Tag not found.'], 404);
        }
        return new TagResource($tag);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/', // Validate hex color
        ]);

        // Check if the name already exists in the tags table
        if (\DB::table('tags')->where('name', $data['name'])->exists()) {
            return response()->json(['error' => 'The tag with this name already exists.'], 422);
        }

        try {
            $tag = $this->tagService->createTag($data);

            return new TagResource($tag);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/', // Validate hex color
        ]);

        // Check if the name already exists in the tags table (excluding the current tag)
        if (\DB::table('tags')->where('name', $data['name'])->where('id', '!=', $id)->exists()) {
            return response()->json(['error' => 'The tag with this name already exists.'], 422);
        }

        try {
            $tag = $this->tagService->updateTag($id, $data);
            if (!$tag) {
                return response()->json(['error' => 'Tag not found.'], 404);
            }

            return new TagResource($tag);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }


    public function destroy($id)
    {
        $tag = $this->tagService->getTagById($id);
        if (!$tag) {
            return response()->json(['error' => 'Tag not found.'], 404);
        }
        $this->tagService->deleteTag($id);
        return response()->json(null, 204);
    }
}
