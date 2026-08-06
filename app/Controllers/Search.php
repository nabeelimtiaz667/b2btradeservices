<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;

class Search extends BaseController
{
    /**
     * Clean-URL search: /search/{keyword}.
     *
     * Global search has no additional filters (the header's "type" dropdown
     * routes to a different controller entirely, see below), so this only
     * ever decodes a single keyword segment -- no need for the labeled
     * key/value parsing Buyer/Product/Supplier::search() use.
     *
     * The old ?q=...&type=... form still works (a submitted GET form can only
     * produce a query string) but 301s rather than rendering: when a specific
     * type is chosen it redirects straight to that controller's own clean
     * URL, not through its query-string form, so this never produces two
     * redirect hops for the single most common search action on the site.
     */
    public function index(...$segments)
    {
        // See Buyer::search() for why this is variadic rather than a single
        // $pathParams argument: CI4 re-splits an (:any) capture on '/' before
        // binding to a real controller method's parameters.
        $pathParams = $segments === [] ? null : implode('/', $segments);

        if ($pathParams === null && service('request')->getUri()->getQuery() !== '') {
            $keyword = $this->request->getGet('q');
            $type    = $this->request->getGet('type') ?? 'all';

            if (empty($keyword)) {
                return redirect()->to(base_url());
            }

            if ($type === 'suppliers') {
                return redirect()->to(base_url('supplier/search/' . search_slug_encode($keyword)), 301);
            }

            if ($type === 'buyers') {
                return redirect()->to(base_url('buyer/search/' . search_slug_encode($keyword)), 301);
            }

            if ($type === 'products') {
                return redirect()->to(base_url('product/search/' . search_slug_encode($keyword)), 301);
            }

            return redirect()->to(base_url('search/' . search_slug_encode($keyword)), 301);
        }

        if (empty($pathParams)) {
            return redirect()->to(base_url());
        }

        $keyword = search_slug_decode($pathParams);

        $userModel = new UserModel();
        $productModel = new ProductModel();
        $inquiryModel = new BuyerInquiryModel();
        $categoryModel = new CategoryModel();
        $countryModel = new CountryModel();

        $suppliers = $userModel
            ->where('user_type', 'supplier')
            ->where('status', 'approved')
            ->groupStart()
                ->like('company_name', $keyword)
                ->orLike('selling_products', $keyword)
                ->orLike('name', $keyword)
            ->groupEnd()
            ->orderBy('membership_level', 'DESC')
            ->limit(6)
            ->findAll();

        foreach ($suppliers as &$s) {
            $s['country'] = $countryModel->find($s['country_id']);
        }

        $products = $productModel
            ->where('status', 'active')
            ->groupStart()
                ->like('name', $keyword)
                ->orLike('description', $keyword)
            ->groupEnd()
            ->orderBy('is_featured', 'DESC')
            ->limit(6)
            ->findAll();

        foreach ($products as &$p) {
            $p['supplier'] = $userModel->find($p['supplier_id']);
            $p['category'] = $categoryModel->find($p['category_id']);
        }

        $inquiries = $inquiryModel
            ->where('status', 'active')
            ->groupStart()
                ->like('title', $keyword)
                ->orLike('product_name', $keyword)
                ->orLike('description', $keyword)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->findAll();

        foreach ($inquiries as &$inq) {
            $inq['category'] = $categoryModel->find($inq['category_id']);
            $inq['country'] = $countryModel->find($inq['country_id']);
        }

        $data = [
            // Not esc()'d here: the layout already escapes 'title' exactly
            // once. Pre-escaping it here would double-encode any keyword
            // containing '&', '<', etc. (e.g. rendering "&amp;amp;").
            'title' => 'Search Results for "' . $keyword . '"',
            'metaDescription' => 'Suppliers, products, and buyer inquiries matching "' . $keyword . '" on B2B Trade Services.',
            'canonical' => current_url(),
            'keyword' => $keyword,
            'suppliers' => $suppliers,
            'products' => $products,
            'inquiries' => $inquiries,
        ];

        return view('pages/search-results', $data);
    }
}
