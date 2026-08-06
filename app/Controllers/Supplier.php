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

        // '/supplier-profile' (no id) and '/supplier' route to this same
        // method and render identical content -- the former is never linked
        // internally (checked: no href anywhere points to it), so this
        // canonicalizes to '/supplier' regardless of which URL was used to
        // reach it, while still preserving the pagination query string.
        $query = service('request')->getUri()->getQuery();

        $data = [
            'title' => 'Suppliers',
            'metaDescription' => 'Find verified exporters, global suppliers, and worldwide recognized companies on B2B Trade Services.',
            'canonical' => base_url('supplier') . ($query !== '' ? '?' . $query : ''),
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

        $supplierDisplayName = $supplier['company_name'] ?? $supplier['name'];

        if (!empty($supplier['company_introduction'])) {
            $supplierMetaDescription = truncate_for_meta($supplier['company_introduction']);
        } elseif (!empty($supplier['selling_products'])) {
            $supplierMetaDescription = truncate_for_meta(
                $supplierDisplayName . ' on B2B Trade Services, dealing in: ' . $supplier['selling_products']
            );
        } else {
            $supplierMetaDescription = $supplierDisplayName . ' - verified supplier profile on B2B Trade Services.';
        }

        $data = [
            'title' => $supplierDisplayName,
            'metaDescription' => $supplierMetaDescription,
            'canonical' => current_url(),
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

        $catQuery = service('request')->getUri()->getQuery();

        $data = [
            'title' => $category ? 'Suppliers in ' . $category['name'] : 'Find Suppliers By Category',
            'metaDescription' => $category
                ? 'Find verified suppliers in ' . $category['name'] . ' on B2B Trade Services.'
                : 'Browse suppliers by category on B2B Trade Services and find the right manufacturer or exporter for your needs.',
            'canonical' => current_url() . ($catQuery !== '' ? '?' . $catQuery : ''),
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

        $countryQuery = service('request')->getUri()->getQuery();

        $data = [
            'title' => $country ? 'Suppliers in ' . $country['name'] : 'Find Suppliers By Country',
            'metaDescription' => $country
                ? 'Find verified suppliers in ' . $country['name'] . ' on B2B Trade Services.'
                : 'Browse suppliers by country and region on B2B Trade Services and find the right trade partner near you.',
            'canonical' => current_url() . ($countryQuery !== '' ? '?' . $countryQuery : ''),
            'country' => $country,
            'suppliers' => $suppliers,
            'pager' => $this->userModel->pager,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ];

        return view('pages/supplier-country', $data);
    }

    /**
     * Clean-URL search: /supplier/search/{keyword}/country/{code}/membership/{level}.
     * See Buyer::search() for the full explanation of the redirect pattern.
     */
    public function search(...$segments)
    {
        // See Buyer::search() for why this is variadic rather than a single
        // $pathParams argument: CI4 re-splits an (:any) capture on '/' before
        // binding to a real controller method's parameters.
        $pathParams = $segments === [] ? null : implode('/', $segments);

        $knownKeys = ['country', 'membership'];

        if ($pathParams === null && service('request')->getUri()->getQuery() !== '') {
            $filters = [];

            if ($countryId = $this->request->getGet('country')) {
                $country = $this->countryModel->find($countryId);
                if (! empty($country['code'])) {
                    $filters['country'] = $country['code'];
                }
            }

            if ($membership = $this->request->getGet('membership')) {
                $filters['membership'] = $membership;
            }

            $clean = build_search_path($this->request->getGet('q'), $filters);

            return redirect()->to(base_url('supplier/search' . ($clean !== '' ? '/' . $clean : '')), 301);
        }

        $parsed  = parse_search_path($pathParams, $knownKeys);
        $keyword = $parsed['keyword'];
        $filters = $parsed['filters'];

        $countryId = null;
        if (! empty($filters['country'])) {
            $country = $this->countryModel->getCountryByCode($filters['country']);
            $countryId = $country['id'] ?? null;
        }

        $membership = $filters['membership'] ?? null;

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
            'title' => $keyword ? 'Search Results for "' . $keyword . '" - Suppliers' : 'Search Suppliers',
            'metaDescription' => $keyword
                ? 'Suppliers matching "' . $keyword . '" on B2B Trade Services.'
                : 'Search verified suppliers and manufacturers on B2B Trade Services by keyword, country, or membership.',
            'canonical' => current_url(),
            'suppliers' => $suppliers,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'searchKeyword' => $keyword,
        ];

        return view('pages/supplier', $data);
    }
}
