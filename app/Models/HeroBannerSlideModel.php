<?php

namespace App\Models;

use CodeIgniter\Model;

class HeroBannerSlideModel extends Model
{
    protected $table = 'hero_banner_slides';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['image_filename', 'file_type', 'link_url', 'sort_order', 'status'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveSlides()
    {
        return $this->where('status', 'active')->orderBy('sort_order', 'ASC')->findAll();
    }

    public function getHistorySlides()
    {
        return $this->where('status', 'history')->orderBy('updated_at', 'DESC')->findAll();
    }
}
