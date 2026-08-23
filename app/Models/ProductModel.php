<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'supplier_id', 'category_id', 'name', 'slug', 'description', 'specifications',
        'main_image', 'gallery_images', 'min_order_quantity', 'min_order_unit',
        'price_range', 'supply_ability', 'delivery_time', 'packaging', 'port',
        'payment_terms', 'certifications', 'is_featured', 'status'
    ];
    // 'featured_set' deliberately NOT in $allowedFields -- it drove manual
    // pinning into a specific Top Products carousel set, removed 2026-08-23
    // (Top Products is now fully automatic). Column stays in the DB
    // (harmless, unused) rather than dropping it; nothing should write to
    // it anymore.
    // 'view_count' deliberately NOT in $allowedFields -- it's a system-managed
    // popularity counter (see Product::detail(), Pages.php's Top Products
    // ranking), not a form field. Keeping it out of mass assignment means an
    // admin edit form can never accidentally zero it or a stray POST field
    // can't inflate it; it's only ever touched via a raw atomic increment.
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveProducts($limit = null)
    {
        $builder = $this->where('status', 'active')->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getProductBySlug($slug)
    {
        return $this->where('slug', $slug)->where('status', 'active')->first();
    }

    public function getProductsBySupplier($supplierId, $limit = null)
    {
        $builder = $this->where('supplier_id', $supplierId)->where('status', 'active');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getProductsByCategory($categoryId, $limit = null)
    {
        $builder = $this->where('category_id', $categoryId)->where('status', 'active');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getFeaturedProducts($limit = 10)
    {
        return $this->where('is_featured', 1)->where('status', 'active')->limit($limit)->findAll();
    }

    public function searchProducts($keyword)
    {
        return $this->like('name', $keyword)
            ->orLike('description', $keyword)
            ->orLike('specifications', $keyword)
            ->where('status', 'active')
            ->findAll();
    }

    public function getProductWithRelations($id)
    {
        $product = $this->find($id);
        if ($product) {
            $userModel = new UserModel();
            $categoryModel = new CategoryModel();
            $product['supplier'] = $userModel->find($product['supplier_id']);
            $product['category'] = $categoryModel->find($product['category_id']);
        }
        return $product;
    }
}
