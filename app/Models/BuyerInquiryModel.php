<?php

namespace App\Models;

use CodeIgniter\Model;

class BuyerInquiryModel extends Model
{
    protected $table = 'buyer_inquiries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id', 'category_id', 'country_id', 'title', 'description',
        'product_name', 'quantity', 'unit', 'target_price', 'buyer_name',
        'buyer_email', 'buyer_phone', 'buyer_whatsapp', 'buyer_company',
        'shipping_terms', 'payment_terms', 'destination_port',
        'validity_date', 'inquiry_date', 'attachment',
        'is_featured', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveInquiries($limit = null)
    {
        $builder = $this->where('status', 'active')->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getInquiriesByCategory($categoryId, $limit = null)
    {
        $builder = $this->where('category_id', $categoryId)->where('status', 'active');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getInquiriesByCountry($countryId, $limit = null)
    {
        $builder = $this->where('country_id', $countryId)->where('status', 'active');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getInquiriesByUser($userId)
    {
        return $this->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
    }

    public function getFeaturedInquiries($limit = 10)
    {
        return $this->where('is_featured', 1)->where('status', 'active')->limit($limit)->findAll();
    }

    public function searchInquiries($keyword)
    {
        return $this->like('title', $keyword)
            ->orLike('product_name', $keyword)
            ->orLike('description', $keyword)
            ->where('status', 'active')
            ->findAll();
    }

    public function getInquiryWithRelations($id)
    {
        $inquiry = $this->find($id);
        if ($inquiry) {
            $categoryModel = new CategoryModel();
            $countryModel = new CountryModel();
            $inquiry['category'] = $categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $countryModel->find($inquiry['country_id']);
        }
        return $inquiry;
    }
}
