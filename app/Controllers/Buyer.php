<?php

namespace App\Controllers;

use App\Models\BuyerInquiryModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;

class Buyer extends BaseController
{
    protected $inquiryModel;
    protected $userModel;
    protected $categoryModel;
    protected $countryModel;

    public function __construct()
    {
        $this->inquiryModel = new BuyerInquiryModel();
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
        $this->countryModel = new CountryModel();
    }

    private function getFeaturedSuppliers($limit = 3)
    {
        $suppliers = $this->userModel
            ->where('user_type', 'supplier')
            ->where('status', 'approved')
            ->orderBy('membership_level', 'DESC')
            ->limit($limit)
            ->findAll();

        foreach ($suppliers as &$s) {
            $s['country'] = $this->countryModel->find($s['country_id']);
        }

        return $suppliers;
    }

    public function index()
    {
        $inquiries = $this->inquiryModel
            ->where('status', 'active')
            ->orderBy('inquiry_date', 'DESC')
            ->orderBy('is_featured', 'DESC')
            ->paginate(50, 'buyer');

        foreach ($inquiries as &$inquiry) {
            $inquiry['category'] = $this->categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $this->countryModel->find($inquiry['country_id']);
        }

        $pager = $this->inquiryModel->pager;

        $data = [
            'title' => 'Buyer Inquiries',
            'inquiries' => $inquiries,
            'pager' => $pager,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'featuredSuppliers' => $this->getFeaturedSuppliers(),
        ];

        return view('pages/buyer-main', $data);
    }

    protected function inquirySlug(string $title): string
    {
        return url_title(strtolower($title), '-', true);
    }

    public function legacyRedirect($id = null)
    {
        if (!$id) {
            return redirect()->to('/buyers');
        }
        $inquiry = $this->inquiryModel->find($id);
        if (!$inquiry) {
            return redirect()->to('/buyers');
        }
        $slug = $this->inquirySlug($inquiry['title']);
        return redirect()->to(base_url('buyer-inquiry/' . $slug . '/' . $id), 301);
    }

    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('/buyer');
        }

        $inquiry = $this->inquiryModel->find($id);

        if (!$inquiry || $inquiry['status'] !== 'active') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inquiry not found');
        }

        $inquiry['category'] = $this->categoryModel->find($inquiry['category_id']);
        $inquiry['country'] = $this->countryModel->find($inquiry['country_id']);

        $relatedInquiries = $this->inquiryModel->getInquiriesByCategory($inquiry['category_id'], 5);

        foreach ($relatedInquiries as &$related) {
            $related['country'] = $this->countryModel->find($related['country_id']);
        }

        $data = [
            'title' => $inquiry['title'],
            'inquiry' => $inquiry,
            'relatedInquiries' => $relatedInquiries,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ];

        return view('pages/buyer-detail', $data);
    }

    public function postRfq()
    {
        $data = [
            'title' => 'Post a Buy Offer',
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ];
        return view('pages/post-rfq', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');
        $countryId = $this->request->getGet('country');
        $date = $this->request->getGet('date');

        $builder = $this->inquiryModel->where('status', 'active');

        if ($keyword) {
            $builder->groupStart()
                ->like('title', $keyword)
                ->orLike('product_name', $keyword)
                ->orLike('description', $keyword)
                ->groupEnd();
        }

        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        if ($countryId) {
            $builder->where('country_id', $countryId);
        }

        if ($date) {
            $builder->where('DATE(created_at) >=', $date);
        }

        $inquiries = $builder->orderBy('inquiry_date', 'DESC')->findAll();

        foreach ($inquiries as &$inquiry) {
            $inquiry['category'] = $this->categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $this->countryModel->find($inquiry['country_id']);
        }

        $data = [
            'title' => 'Search Results',
            'inquiries' => $inquiries,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'featuredSuppliers' => $this->getFeaturedSuppliers(),
            'searchKeyword' => $keyword,
        ];

        return view('pages/buyer-main', $data);
    }

    public function category($slug = null)
    {
        $inquiries = [];
        $categoryName = 'All Categories';
        
        if ($slug) {
            $category = $this->categoryModel->where('slug', $slug)->first();
            
            if (!$category) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');
            }
            
            $inquiries = $this->inquiryModel
                ->where('status', 'active')
                ->where('category_id', $category['id'])
                ->orderBy('inquiry_date', 'DESC')
                ->findAll();
            
            $categoryName = esc($category['name']);
        } else {
            $inquiries = $this->inquiryModel
                ->where('status', 'active')
                ->orderBy('inquiry_date', 'DESC')
                ->findAll();
        }

        foreach ($inquiries as &$inquiry) {
            $inquiry['category'] = $this->categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $this->countryModel->find($inquiry['country_id']);
        }

        $data = [
            'title' => $categoryName . ' - Buyer Inquiries',
            'inquiries' => $inquiries,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'featuredSuppliers' => $this->getFeaturedSuppliers(),
            'currentCategory' => $categoryName,
        ];

        return view('pages/buyer-main', $data);
    }
}
