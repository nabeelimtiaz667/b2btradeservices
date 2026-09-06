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

        $builder = $this->productModel->where('status', 'active');

        if ($supplierId) {
            $builder->where('supplier_id', $supplierId)->orderBy('created_at', 'DESC');
        } else {
            $builder->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC');
        }

        $products = $builder->paginate(12, 'product');

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
            'metaDescription' => $supplierName
                ? 'Browse products from ' . $supplierName . ' on B2B Trade Services.'
                : 'Browse products from verified suppliers and manufacturers on B2B Trade Services.',
            // ?supplier= and /product/supplier/{id} render identical content;
            // /product/supplier/{id} is the form actually linked from supplier
            // profiles (see supplier-profile.php), so the query-param form
            // canonicalizes to it rather than self-referencing a URL nothing
            // links to.
            'canonical' => $supplierId ? base_url('product/supplier/' . $supplierId) : current_url(),
            'products' => $products,
            'pager' => $this->productModel->pager,
            'resultsTotal' => $this->productModel->pager->getTotal('product'),
            'categories' => $this->categoryModel->getActiveCategories(),
        ];

        $tier = $this->contentAccessTier();
        $data['gateTier'] = ($this->productModel->pager->getCurrentPage('product') > 1 && $tier !== 'privileged') ? $tier : null;

        return view('pages/product', $data);
    }

    public function bySupplier($supplierId = null)
    {
        if (!$supplierId) {
            return redirect()->to('/product');
        }

        $products = $this->productModel
            ->where('status', 'active')
            ->where('supplier_id', $supplierId)
            ->orderBy('created_at', 'DESC')
            ->paginate(12, 'product');

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
            'metaDescription' => $supplierName
                ? 'Browse products from ' . $supplierName . ' on B2B Trade Services.'
                : 'Browse products from verified suppliers and manufacturers on B2B Trade Services.',
            'canonical' => current_url(),
            'products' => $products,
            'pager' => $this->productModel->pager,
            'resultsTotal' => $this->productModel->pager->getTotal('product'),
            'categories' => $this->categoryModel->getActiveCategories(),
        ];

        $tier = $this->contentAccessTier();
        $data['gateTier'] = ($this->productModel->pager->getCurrentPage('product') > 1 && $tier !== 'privileged') ? $tier : null;

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

        $this->trackView('products', (int) $product['id'], 'view_count', 'viewed_products');

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
            'metaDescription' => !empty($product['description'])
                ? truncate_for_meta($product['description'])
                : $product['name'] . ' - available from verified suppliers on B2B Trade Services.',
            'canonical' => current_url(),
            'product' => $product,
            'supplierProducts' => $supplierProducts,
            'relatedProducts' => $relatedProducts,
        ];

        return view('pages/product-detail', $data);
    }

    /**
     * Clean-URL search: /product/search/{keyword}/category/{slug}.
     * See Buyer::search() for the full explanation of the redirect pattern.
     */
    public function search(...$segments)
    {
        // See Buyer::search() for why this is variadic rather than a single
        // $pathParams argument: CI4 re-splits an (:any) capture on '/' before
        // binding to a real controller method's parameters.
        $pathParams = $segments === [] ? null : implode('/', $segments);

        $knownKeys = ['category', 'page'];

        if ($pathParams === null && service('request')->getUri()->getQuery() !== '') {
            $filters = [];

            if ($categoryId = $this->request->getGet('category')) {
                $cat = $this->categoryModel->find($categoryId);
                if (! empty($cat['slug'])) {
                    $filters['category'] = $cat['slug'];
                }
            }

            $clean = build_search_path($this->request->getGet('q'), $filters);

            return redirect()->to(base_url('product/search' . ($clean !== '' ? '/' . $clean : '')), 301);
        }

        $parsed  = parse_search_path($pathParams, $knownKeys);
        $keyword = $parsed['keyword'];
        $filters = $parsed['filters'];

        $categoryId = null;
        if (! empty($filters['category'])) {
            $cat = $this->categoryModel->getCategoryBySlug($filters['category']);
            $categoryId = $cat['id'] ?? null;
        }

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

        $perPage = 12;
        $total = $builder->countAllResults(false);
        $totalPages = max(1, (int) ceil($total / $perPage));
        // parse_search_path() hands back whatever string was in the URL
        // segment -- (int) cast turns anything non-numeric (or missing) into
        // 0, and the max(1, ...) below floors it to page 1 rather than a
        // negative/zero offset.
        $rawPage = $filters['page'] ?? null;
        $page = min(max(1, (int) ($rawPage ?? 1)), $totalPages);

        // If what was actually requested (page/-1, page/0, page/abc, or an
        // out-of-range page/99999) doesn't match the clamped page being
        // rendered, redirect to the URL for that actual page rather than
        // silently rendering it under the wrong one.
        if ($rawPage !== null && $rawPage !== (string) $page) {
            $canonicalFilters = ['category' => $filters['category'] ?? null];
            $canonicalFilters['page'] = $page > 1 ? (string) $page : null;
            $clean = build_search_path($keyword, $canonicalFilters);

            return redirect()->to(base_url('product/search' . ($clean !== '' ? '/' . $clean : '')), 301);
        }

        $products = $builder
            ->orderBy('is_featured', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($perPage, ($page - 1) * $perPage);

        foreach ($products as &$product) {
            $product['supplier'] = $this->userModel->find($product['supplier_id']);
            $product['category'] = $this->categoryModel->find($product['category_id']);
        }

        $searchPager = build_search_pager('product/search', $keyword, ['category' => $filters['category'] ?? null], $page, $totalPages);

        $data = [
            'title' => $keyword ? 'Search Results for "' . $keyword . '" - Products' : 'Search Products',
            'metaDescription' => $keyword
                ? 'Products matching "' . $keyword . '" on B2B Trade Services.'
                : 'Search products from verified suppliers and manufacturers on B2B Trade Services.',
            'canonical' => current_url(),
            'products' => $products,
            'searchPager' => $searchPager,
            'resultsTotal' => $total,
            'categories' => $this->categoryModel->getActiveCategories(),
            'searchKeyword' => $keyword,
            'selectedCategory' => $categoryId,
        ];

        $tier = $this->contentAccessTier();
        $data['gateTier'] = ($page > 1 && $tier !== 'privileged') ? $tier : null;

        return view('pages/product', $data);
    }
}
