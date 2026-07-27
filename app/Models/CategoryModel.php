<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name', 'slug', 'description', 'image', 'parent_id', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveCategories()
    {
        return $this->where('status', 'active')->orderBy('name', 'ASC')->findAll();
    }

    public function getCategoryBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }

    public function getParentCategories()
    {
        return $this->where('parent_id', null)->where('status', 'active')->findAll();
    }

    public function getSubCategories($parentId)
    {
        return $this->where('parent_id', $parentId)->where('status', 'active')->findAll();
    }
}
