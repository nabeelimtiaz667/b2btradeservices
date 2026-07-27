<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id', 'company_name', 'slug', 'category_id', 'country_id',
        'logo', 'banner_image', 'description', 'business_type', 'main_products',
        'year_established', 'employees', 'annual_revenue', 'address', 'city',
        'state', 'postal_code', 'phone', 'email', 'website', 'membership_type',
        'is_verified', 'is_featured', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getApprovedSuppliers($limit = null)
    {
        $builder = $this->where('status', 'approved')->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getSupplierBySlug($slug)
    {
        return $this->where('slug', $slug)->where('status', 'approved')->first();
    }

    public function getSuppliersByCategory($categoryId, $limit = null)
    {
        $builder = $this->where('category_id', $categoryId)->where('status', 'approved');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getSuppliersByCountry($countryId, $limit = null)
    {
        $builder = $this->where('country_id', $countryId)->where('status', 'approved');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getSuppliersByMembership($membershipType, $limit = null)
    {
        $builder = $this->where('membership_type', $membershipType)->where('status', 'approved');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function searchSuppliers($keyword)
    {
        return $this->like('company_name', $keyword)
            ->orLike('main_products', $keyword)
            ->orLike('description', $keyword)
            ->where('status', 'approved')
            ->findAll();
    }

    public function getFeaturedSuppliers($limit = 10)
    {
        return $this->where('is_featured', 1)->where('status', 'approved')->limit($limit)->findAll();
    }

    public function getSupplierWithRelations($id)
    {
        $supplier = $this->find($id);
        if ($supplier) {
            $categoryModel = new CategoryModel();
            $countryModel = new CountryModel();
            $supplier['category'] = $categoryModel->find($supplier['category_id']);
            $supplier['country'] = $countryModel->find($supplier['country_id']);
        }
        return $supplier;
    }
}
