<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;
use App\Models\ProductModel;

class Supplier extends BaseController
{
    protected $userModel;
    protected $categoryModel;
    protected $countryModel;
    protected $productModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
        $this->countryModel = new CountryModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $suppliers = $this->userModel
            ->where('user_type', 'supplier')
            ->where('status', 'approved')
            ->orderBy('membership_level', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate(12, 'supplier');

        foreach ($suppliers as &$supplier) {
            $supplier['country'] = !empty($supplier['country_id']) ? $this->countryModel->find($supplier['country_id']) : null;
            $supplier['products'] = $this->productModel
                ->where('supplier_id', $supplier['id'])
                ->where('status', 'active')
                ->limit(3)
                ->findAll();
        }

        $data = [
            'title' => 'Suppliers',
            'suppliers' => $suppliers,
            'pager' => $this->userModel->pager,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ];

        return view('pages/supplier', $data);
    }

    public function profile($slug = null)
    {
        if (!$slug) {
            return redirect()->to('/supplier');
        }

        if (is_numeric($slug)) {
            $supplier = $this->userModel
                ->where('user_type', 'supplier')
                ->where('status', 'approved')
                ->where('id', $slug)
                ->first();
            if ($supplier && !empty($supplier['slug'])) {
                return redirect()->to(base_url('supplier/profile/' . $supplier['slug']), 301);
            }
        } else {
            $supplier = $this->userModel
                ->where('user_type', 'supplier')
                ->where('status', 'approved')
                ->where('slug', $slug)
                ->first();
        }

        if (!$supplier) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Supplier not found');
        }

        $supplier['country'] = !empty($supplier['country_id']) ? $this->countryModel->find($supplier['country_id']) : null;

        $products = $this->productModel
            ->where('supplier_id', $supplier['id'])
            ->where('status', 'active')
            ->findAll();

        $productsByCategory = [];
        foreach ($products as $product) {
            $catId = $product['category_id'] ?? 0;
            if (!isset($productsByCategory[$catId])) {
                $cat = $this->categoryModel->find($catId);
                $productsByCategory[$catId] = [
                    'name' => $cat ? $cat['name'] : 'General',
                    'products' => [],
                ];
            }
            $productsByCategory[$catId]['products'][] = $product;
        }

        $interestKeywords = [];
        $sellingProducts = $supplier['selling_products'] ?? '';
        if (!empty($sellingProducts)) {
            $keywords = array_map('trim', explode(',', $sellingProducts));
            foreach ($keywords as $kw) {
                if (!empty($kw)) {
                    $interestKeywords[] = $kw . ' Buyers';
                    $interestKeywords[] = $kw . ' Suppliers';
                }
            }
        }

        $relatedSuppliers = $this->userModel
            ->where('user_type', 'supplier')
            ->where('status', 'approved')
            ->where('id !=', $supplier['id'])
            ->limit(4)
            ->findAll();

        foreach ($relatedSuppliers as &$related) {
            $related['country'] = !empty($related['country_id']) ? $this->countryModel->find($related['country_id']) : null;
        }

        $data = [
            'title' => $supplier['company_name'] ?? $supplier['name'],
            'supplier' => $supplier,
            'products' => $products,
            'productsByCategory' => $productsByCategory,
            'interestKeywords' => $interestKeywords,
            'relatedSuppliers' => $relatedSuppliers,
        ];

        return view('pages/supplier-profile', $data);
    }

    public function category($slug = null)
    {
        $category = null;

        if ($slug) {
            $category = $this->categoryModel->getCategoryBySlug($slug);
            if (!$category) {
                return redirect()->to('/supplier');
            }
        }

        $builder = $this->userModel
            ->where('user_type', 'supplier')
            ->where('status', 'approved');

        if ($category) {
            $db = \Config\Database::connect();
            $subQuery = $db->table('products')
                ->select('supplier_id')
                ->where('category_id', $category['id'])
                ->where('status', 'active')
                ->getCompiledSelect();
            $builder->where("id IN ($subQuery)", null, false);
        }

        $suppliers = $builder
            ->orderBy('membership_level', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate(12, 'supplier');

        foreach ($suppliers as &$supplier) {
            $supplier['country'] = !empty($supplier['country_id']) ? $this->countryModel->find($supplier['country_id']) : null;
            $supplier['products'] = $this->productModel
                ->where('supplier_id', $supplier['id'])
                ->where('status', 'active')
                ->limit(3)
                ->findAll();
            if ($category) {
                $supplier['category'] = $category;
            } else {
                $catId = $supplier['products'][0]['category_id'] ?? null;
                $supplier['category'] = $catId ? $this->categoryModel->find($catId) : null;
            }
        }

        $data = [
            'title' => $category ? $category['name'] : 'Find Suppliers By Category',
            'category' => $category,
            'suppliers' => $suppliers,
            'pager' => $this->userModel->pager,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ];

        return view('pages/supplier-category', $data);
    }

    public function country($code = null)
    {
        $country = null;

        $builder = $this->userModel
            ->where('user_type', 'supplier')
            ->where('status', 'approved');

        if ($code) {
            $country = $this->countryModel->getCountryByCode($code);
            if ($country) {
                $builder->where('country_id', $country['id']);
            }
        }

        $suppliers = $builder->orderBy('created_at', 'DESC')->paginate(12, 'supplier');

        foreach ($suppliers as &$supplier) {
            $supplier['country'] = !empty($supplier['country_id']) ? $this->countryModel->find($supplier['country_id']) : null;
            $supplier['products'] = $this->productModel
                ->where('supplier_id', $supplier['id'])
                ->where('status', 'active')
                ->limit(3)
                ->findAll();
        }

        $data = [
            'title' => $country ? 'Suppliers in ' . $country['name'] : 'Find Suppliers By Country',
            'country' => $country,
            'suppliers' => $suppliers,
            'pager' => $this->userModel->pager,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ];

        return view('pages/supplier-country', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $countryId = $this->request->getGet('country');
        $membership = $this->request->getGet('membership');

        $builder = $this->userModel
            ->where('user_type', 'supplier')
            ->where('status', 'approved');

        if ($keyword) {
            $builder->groupStart()
                ->like('company_name', $keyword)
                ->orLike('selling_products', $keyword)
                ->orLike('name', $keyword)
                ->groupEnd();
        }

        if ($countryId) {
            $builder->where('country_id', $countryId);
        }

        if ($membership) {
            $builder->where('membership_level', $membership);
        }

        $suppliers = $builder->orderBy('membership_level', 'DESC')->orderBy('created_at', 'DESC')->findAll();

        foreach ($suppliers as &$supplier) {
            $supplier['country'] = !empty($supplier['country_id']) ? $this->countryModel->find($supplier['country_id']) : null;
            $supplier['products'] = $this->productModel
                ->where('supplier_id', $supplier['id'])
                ->where('status', 'active')
                ->limit(3)
                ->findAll();
        }

        $data = [
            'title' => 'Search Results',
            'suppliers' => $suppliers,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'searchKeyword' => $keyword,
        ];

        return view('pages/supplier', $data);
    }
}
