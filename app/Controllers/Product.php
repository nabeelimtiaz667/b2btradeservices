<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;

class Product extends BaseController
{
    protected $productModel;
    protected $userModel;
    protected $categoryModel;
    protected $countryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
        $this->countryModel = new CountryModel();
    }

    public function index()
    {
        $supplierId = $this->request->getGet('supplier');

        if ($supplierId) {
            $products = $this->productModel->where('status', 'active')->where('supplier_id', $supplierId)->orderBy('created_at', 'DESC')->findAll();
        } else {
            $products = $this->productModel->getActiveProducts();
        }

        $supplierName = null;
        foreach ($products as &$product) {
            $product['supplier'] = $this->userModel->find($product['supplier_id']);
            $product['category'] = $this->categoryModel->find($product['category_id']);
            if ($supplierId && !$supplierName && $product['supplier']) {
                $supplierName = $product['supplier']['company_name'] ?? $product['supplier']['name'];
            }
        }

        $data = [
            'title' => $supplierName ? 'Products by ' . $supplierName : 'Products',
            'products' => $products,
            'categories' => $this->categoryModel->getActiveCategories(),
        ];

        return view('pages/product', $data);
    }

    public function bySupplier($supplierId = null)
    {
        if (!$supplierId) {
            return redirect()->to('/product');
        }

        $products = $this->productModel->where('status', 'active')->where('supplier_id', $supplierId)->orderBy('created_at', 'DESC')->findAll();

        $supplierName = null;
        foreach ($products as &$product) {
            $product['supplier'] = $this->userModel->find($product['supplier_id']);
            $product['category'] = $this->categoryModel->find($product['category_id']);
            if (!$supplierName && $product['supplier']) {
                $supplierName = $product['supplier']['company_name'] ?? $product['supplier']['name'];
            }
        }

        $data = [
            'title' => $supplierName ? 'Products by ' . $supplierName : 'Products',
            'products' => $products,
            'categories' => $this->categoryModel->getActiveCategories(),
        ];

        return view('pages/product', $data);
    }

    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('/product');
        }

        $product = $this->productModel->getProductWithRelations($id);

        if (!$product || (isset($product['status']) && $product['status'] !== 'active')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        if (!empty($product['supplier']) && !empty($product['supplier']['country_id'])) {
            $product['supplier']['country'] = $this->countryModel->find($product['supplier']['country_id']);
        }

        $supplierProducts = [];
        if (!empty($product['supplier_id'])) {
            $supplierProducts = $this->productModel
                ->where('supplier_id', $product['supplier_id'])
                ->where('status', 'active')
                ->where('id !=', $product['id'])
                ->orderBy('created_at', 'DESC')
                ->limit(6)
                ->findAll();
            foreach ($supplierProducts as &$sp) {
                $sp['supplier'] = $product['supplier'];
                $sp['category'] = $this->categoryModel->find($sp['category_id']);
            }
        }

        $relatedProducts = [];
        if (!empty($product['category_id'])) {
            $relatedProducts = $this->productModel->getProductsByCategory($product['category_id'], 5);
            $relatedProducts = array_filter($relatedProducts, function ($p) use ($product) {
                return $p['id'] != $product['id'];
            });
            $relatedProducts = array_slice($relatedProducts, 0, 4);
            foreach ($relatedProducts as &$related) {
                $related['supplier'] = $this->userModel->find($related['supplier_id']);
                $related['category'] = $this->categoryModel->find($related['category_id']);
            }
        }

        $data = [
            'title' => $product['name'],
            'product' => $product,
            'supplierProducts' => $supplierProducts,
            'relatedProducts' => $relatedProducts,
        ];

        return view('pages/product-detail', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');

        $builder = $this->productModel->where('status', 'active');

        if ($keyword) {
            $builder->groupStart()
                ->like('name', $keyword)
                ->orLike('description', $keyword)
                ->orLike('specifications', $keyword)
                ->groupEnd();
        }

        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        $products = $builder->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC')->findAll();

        foreach ($products as &$product) {
            $product['supplier'] = $this->userModel->find($product['supplier_id']);
            $product['category'] = $this->categoryModel->find($product['category_id']);
        }

        $data = [
            'title' => 'Search Results',
            'products' => $products,
            'categories' => $this->categoryModel->getActiveCategories(),
            'searchKeyword' => $keyword,
            'selectedCategory' => $categoryId,
        ];

        return view('pages/product', $data);
    }
}
