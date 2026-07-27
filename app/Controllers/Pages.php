<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CountryModel;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;

class Pages extends BaseController
{
    public function index($page = 'index')
    {
        if (! is_file(APPPATH . 'Views/pages/' . $page . '.php')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }

        $data['title'] = ($page === 'index') ? null : ucfirst(str_replace('-', ' ', $page));

        if ($page === 'index') {
            $categoryModel = new CategoryModel();

            $countryModel = new CountryModel();
            $userModel = new UserModel();
            $productModel = new ProductModel();
            $inquiryModel = new BuyerInquiryModel();

            $data['categories'] = $categoryModel->getActiveCategories();
            $data['countries'] = $countryModel->getActiveCountries();

            $membershipOrder = "FIELD(membership_level, 'platinum', 'gold', 'silver', 'starter', 'free')";
            $featuredSuppliers = $userModel
                ->where('user_type', 'supplier')
                ->where('status', 'approved')
                ->where('is_featured', 1)
                ->orderBy($membershipOrder)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            foreach ($featuredSuppliers as &$s) {
                $s['country'] = $countryModel->find($s['country_id']);
                $s['products'] = $productModel
                    ->where('supplier_id', $s['id'])
                    ->where('status', 'active')
                    ->limit(3)
                    ->findAll();
            }
            unset($s);

            if (count($featuredSuppliers) > 2) {
                shuffle($featuredSuppliers);
            }

            $data['featuredSuppliers'] = $featuredSuppliers;

            $categorySuppliers = $userModel
                ->where('user_type', 'supplier')
                ->where('status', 'approved')
                ->whereIn('membership_level', ['platinum', 'gold'])
                ->findAll();

            foreach ($categorySuppliers as &$cs) {
                $cs['country'] = $countryModel->find($cs['country_id']);
                $cs['products'] = $productModel
                    ->where('supplier_id', $cs['id'])
                    ->where('status', 'active')
                    ->limit(3)
                    ->findAll();
            }
            unset($cs);

            shuffle($categorySuppliers);

            $data['categorySuppliers'] = $categorySuppliers;

            $topProducts = $productModel
                ->where('status', 'active')
                ->orderBy('is_featured', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->limit(12)
                ->findAll();

            foreach ($topProducts as &$p) {
                $supplier = $userModel->find($p['supplier_id']);
                if ($supplier) {
                    $supplier['country'] = $countryModel->find($supplier['country_id']);
                }
                $p['supplier'] = $supplier;
                $p['category'] = $categoryModel->find($p['category_id']);
            }
            $data['topProducts'] = $topProducts;

            $featuredProducts = $productModel
                ->where('status', 'active')
                ->where('is_featured', 1)
                ->orderBy('created_at', 'DESC')
                ->limit(12)
                ->findAll();

            foreach ($featuredProducts as &$fp) {
                $fp['supplier'] = $userModel->find($fp['supplier_id']);
                $fp['category'] = $categoryModel->find($fp['category_id']);
            }
            $data['featuredProducts'] = $featuredProducts;

            $latestInquiries = $inquiryModel->orderBy('inquiry_date', 'DESC')->getActiveInquiries(8);

            foreach ($latestInquiries as &$inq) {
                if (!empty($inq['country_id'])) {
                    $country = $countryModel->find($inq['country_id']);
                    if ($country) {
                        $inq['country_flag'] = $country['flag'] ?? '';
                        $inq['country_name'] = $country['name'] ?? '';
                    }
                }
            }
            $data['latestInquiries'] = $latestInquiries;

            $data['supplierCount'] = $userModel
                ->where('user_type', 'supplier')
                ->where('status', 'approved')
                ->countAllResults(false);

            $data['buyerCount'] = $userModel
                ->where('user_type', 'buyer')
                ->where('status', 'approved')
                ->countAllResults(false);

            $data['productCount'] = $productModel->where('status', 'active')->countAllResults();
        }

        return view('pages/' . $page, $data);
    }
}
