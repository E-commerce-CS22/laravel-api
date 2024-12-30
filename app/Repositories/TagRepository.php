<?php
namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagRepository
{
    protected $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function getAll()
    {
        return $this->tag->all();
    }

    public function findById($id)
    {
        return $this->tag->find($id);
    }

    public function create(array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->tag->create($data);
    }

    public function update($id, array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $tag = $this->findById($id);
        $tag->update($data);
        return $tag;
    }

    public function delete($id)
    {
        $tag = $this->findById($id);
        return $tag->delete();
    }
}
