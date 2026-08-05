<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;

class Search extends BaseController
{
    public function index()
    {
        $keyword = $this->request->getGet('q');
        $type = $this->request->getGet('type') ?? 'all';

        if (empty($keyword)) {
            return redirect()->to('/');
        }

        if ($type === 'suppliers') {
            return redirect()->to('supplier/search?q=' . urlencode($keyword));
        }

        if ($type === 'buyers') {
            return redirect()->to('buyer/search?q=' . urlencode($keyword));
        }

        if ($type === 'products') {
            return redirect()->to('product/search?q=' . urlencode($keyword));
        }

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
            'canonical' => canonical_self_url(),
            'keyword' => $keyword,
            'suppliers' => $suppliers,
            'products' => $products,
            'inquiries' => $inquiries,
        ];

        return view('pages/search-results', $data);
    }
}
