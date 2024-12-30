<?php
namespace App\Services;

use App\Repositories\TagRepository;
use Illuminate\Support\Str;

class TagService
{
    protected $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getAllTags()
    {
        return $this->tagRepository->getAll();
    }

    public function getTagById($id)
    {
        return $this->tagRepository->findById($id);
    }

    public function createTag(array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->tagRepository->create($data);
    }

    public function updateTag($id, array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->tagRepository->update($id, $data);
    }

    public function deleteTag($id)
    {
        return $this->tagRepository->delete($id);
    }
}
