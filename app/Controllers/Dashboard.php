<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;
use App\Models\SiteSettingModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $categoryModel;
    protected $countryModel;
    protected $productModel;
    protected $inquiryModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
        $this->countryModel = new CountryModel();
        $this->productModel = new ProductModel();
        $this->inquiryModel = new BuyerInquiryModel();
        $this->session = session();
    }



    public function index()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userType = $this->session->get('user_type');

        switch ($userType) {
            case 'admin':
            case 'agent':
                return $this->admin();
            case 'supplier':
                return $this->supplier();
            case 'buyer':
                return $this->buyer();
            default:
                return redirect()->to('/login');
        }
    }

    public function admin()
    {
        if (!$this->session->get('logged_in') || !in_array($this->session->get('user_type'), ['admin', 'agent'])) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $isAgent = $this->session->get('user_type') === 'agent';
        $db = \Config\Database::connect();

        $agentId = $isAgent ? $user['id'] : null;

        $totalBuilder = $db->table('users')->whereIn('user_type', ['supplier', 'buyer']);
        if ($agentId) $totalBuilder->where('assigned_agent_id', $agentId);
        $totalLeads = $totalBuilder->countAllResults();

        $buyerLeads = $db->table('buyer_inquiries')->countAllResults();

        $supplierBuilder = $db->table('users')->where('user_type', 'supplier');
        if ($agentId) $supplierBuilder->where('assigned_agent_id', $agentId);
        $supplierLeads = $supplierBuilder->countAllResults();

        $countries = $this->countryModel->findAll();

        $regionMapping = $this->userModel->getRegionMapping();
        $regionalCounts = [];

        foreach ($regionMapping as $regionName => $codes) {
            $countryIds = [];
            foreach ($countries as $c) {
                if (in_array($c['code'], $codes)) {
                    $countryIds[] = $c['id'];
                }
            }
            if (!empty($countryIds)) {
                $rBuilder = $db->table('users')->whereIn('user_type', ['supplier', 'buyer'])->whereIn('country_id', $countryIds);
                if ($agentId) $rBuilder->where('assigned_agent_id', $agentId);
                $regionalCounts[$regionName] = $rBuilder->countAllResults();
            } else {
                $regionalCounts[$regionName] = 0;
            }
        }

        $membershipCounts = [];
        foreach ($this->userModel->getMembershipLevels() as $key => $label) {
            $sBuilder = $db->table('users')->where('user_type', 'supplier')->where('membership_level', $key);
            if ($agentId) $sBuilder->where('assigned_agent_id', $agentId);
            $sCount = $sBuilder->countAllResults();

            $bBuilder = $db->table('users')->where('user_type', 'buyer')->where('membership_level', $key);
            if ($agentId) $bBuilder->where('assigned_agent_id', $agentId);
            $bCount = $bBuilder->countAllResults();

            $membershipCounts[$key] = [
                'label' => $label,
                'suppliers' => $sCount,
                'buyers' => $bCount,
            ];
        }

        $fsBuilder = $db->table('users')->where('user_type', 'supplier')->where('membership_level', 'free');
        if ($agentId) $fsBuilder->where('assigned_agent_id', $agentId);
        $freeSuppliers = $fsBuilder->countAllResults();

        $fbBuilder = $db->table('users')->where('user_type', 'buyer')->where('membership_level', 'free');
        if ($agentId) $fbBuilder->where('assigned_agent_id', $agentId);
        $freeBuyers = $fbBuilder->countAllResults();

        $psBuilder = $db->table('users')->where('user_type', 'supplier')->where('membership_level !=', 'free');
        if ($agentId) $psBuilder->where('assigned_agent_id', $agentId);
        $premiumSuppliers = $psBuilder->countAllResults();

        $pbBuilder = $db->table('users')->where('user_type', 'buyer')->where('membership_level !=', 'free');
        if ($agentId) $pbBuilder->where('assigned_agent_id', $agentId);
        $premiumBuyers = $pbBuilder->countAllResults();

        $stats = [
            'total_leads' => $totalLeads,
            'buyer_leads' => $buyerLeads,
            'supplier_leads' => $supplierLeads,
            'regional_counts' => $regionalCounts,
            'membership_counts' => $membershipCounts,
            'free_suppliers' => $freeSuppliers,
            'free_buyers' => $freeBuyers,
            'premium_suppliers' => $premiumSuppliers,
            'premium_buyers' => $premiumBuyers,
        ];

        return view('dashboard/admin/index', [
            'title' => 'Lead Management Dashboard',
            'user' => $user,
            'stats' => $stats,
            'is_agent' => $isAgent,
        ]);
    }

    public function chartData()
    {
        if (!$this->session->get('logged_in') || !in_array($this->session->get('user_type'), ['admin', 'agent'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $isAgent = $this->session->get('user_type') === 'agent';
        $db = \Config\Database::connect();
        $agentId = $isAgent ? $user['id'] : null;

        $type = $this->request->getGet('type') ?? 'supplier';
        if (!in_array($type, ['supplier', 'buyer'])) {
            return $this->response->setJSON(['error' => 'Invalid type'])->setStatusCode(400);
        }
        $days = (int)($this->request->getGet('days') ?: 7);
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        if ($from && $to) {
            $start = $from;
            $end = $to;
        } else {
            $end = date('Y-m-d');
            $start = date('Y-m-d', strtotime("-{$days} days"));
        }

        $params = [$start, $end];
        $agentSql = '';
        if ($agentId) {
            $agentSql = 'AND assigned_agent_id = ? ';
            $params[] = $agentId;
        }

        $dailyStats = $db->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM users WHERE user_type = ? AND DATE(created_at) BETWEEN ? AND ? " . $agentSql . "GROUP BY DATE(created_at) ORDER BY date ASC", array_merge([$type], $params))->getResultArray();

        $allDays = [];
        $current = new \DateTime($start);
        $endDt = new \DateTime($end);
        $existingDays = array_column($dailyStats, 'count', 'date');
        while ($current <= $endDt) {
            $d = $current->format('Y-m-d');
            $allDays[] = ['date' => $d, 'count' => (int)($existingDays[$d] ?? 0)];
            $current->modify('+1 day');
        }

        $total = array_sum(array_column($allDays, 'count'));

        return $this->response->setJSON(['data' => $allDays, 'total' => $total]);
    }

    public function users()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        $status = $this->request->getGet('status');
        $type = $this->request->getGet('type');

        $builder = $this->userModel->where('user_type !=', 'admin');

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        if (!empty($type)) {
            $builder->where('user_type', $type);
        }

        $users = $builder->orderBy('created_at', 'DESC')->findAll();
        $total_users = count($users);

        return view('dashboard/admin/users', [
            'title' => 'Manage Users',
            'user' => $user,
            'users' => $users,
            'total_users' => $total_users,
            'filters' => [
                'status' => $status,
                'type' => $type,
            ],
        ]);
    }

    public function supplier()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'supplier') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $productCount = $this->productModel->where('supplier_id', $user['id'])->countAllResults();

        return view('dashboard/supplier/index', [
            'title' => 'Supplier Dashboard',
            'user' => $user,
            'productCount' => $productCount,
        ]);
    }

    public function supplierProducts()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'supplier') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $products = $this->productModel->where('supplier_id', $user['id'])->orderBy('created_at', 'DESC')->findAll();
        foreach ($products as &$product) {
            $product['category'] = $this->categoryModel->find($product['category_id']);
        }

        $categories = $this->categoryModel->getActiveCategories();

        $settingModel = new SiteSettingModel();
        $autoApproveListings = $settingModel->getSetting('auto_approve_listings', '1');

        return view('dashboard/supplier/products', [
            'title' => 'My Products',
            'user' => $user,
            'products' => $products,
            'categories' => $categories,
            'autoApproveListings' => $autoApproveListings,
        ]);
    }

    public function supplierAddProduct()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'supplier') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        $settingModelCheck = new SiteSettingModel();
        $maxProducts = (int) $settingModelCheck->getSetting('max_products_per_supplier', '0');
        if ($maxProducts > 0) {
            $currentCount = $this->productModel->where('supplier_id', $user['id'])->countAllResults();
            if ($currentCount >= $maxProducts) {
                return redirect()->to('/dashboard/supplier/products')->with('error', 'You have reached the maximum limit of ' . $maxProducts . ' products. Please contact admin to increase your limit.');
            }
        }

        if ($this->request->getMethod() === 'POST') {
            $contentToCheck = $this->request->getPost('name') . ' ' . $this->request->getPost('description') . ' ' . $this->request->getPost('specifications');
            $restrictedWord = check_restricted_keywords($contentToCheck);
            if ($restrictedWord !== false) {
                return redirect()->back()->withInput()->with('error', 'Your product contains a restricted keyword: "' . $restrictedWord . '". Please remove it and try again.');
            }

            $data = [
                'supplier_id' => $user['id'],
                'category_id' => $this->request->getPost('category_id'),
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'specifications' => $this->request->getPost('specifications'),
                'price_range' => $this->request->getPost('price_range'),
                'min_order_quantity' => $this->request->getPost('min_order_quantity'),
                'min_order_unit' => $this->request->getPost('min_order_unit'),
                'supply_ability' => $this->request->getPost('supply_ability'),
                'delivery_time' => $this->request->getPost('delivery_time'),
                'packaging' => $this->request->getPost('packaging'),
                'port' => $this->request->getPost('port'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'certifications' => $this->request->getPost('certifications'),
                'is_featured' => 0,
                'status' => 'active',
            ];

            $settingModel = new SiteSettingModel();
            $autoApprove = $settingModel->getSetting('auto_approve_listings', '1');
            if ($autoApprove !== '1') {
                $data['status'] = 'pending';
            }

            $mainImage = $this->request->getFile('main_image');
            if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
                $newName = $mainImage->getRandomName();
                $mainImage->move('uploads/products', $newName);
                $data['main_image'] = $newName;
            }

            if ($this->productModel->insert($data)) {
                helper('email');
                notifyAdminNewListing('Product', $data);
                $msg = $autoApprove === '1' ? 'Product added successfully.' : 'Product submitted for review. It will be visible once approved by admin.';
                return redirect()->to('/dashboard/supplier/products')->with('success', $msg);
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to add product.');
            }
        }

        return view('dashboard/supplier/product-form', [
            'title' => 'Add Product',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'product' => null,
        ]);
    }

    public function supplierEditProduct($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'supplier') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        $product = $this->productModel->find($id);

        if (!$product || $product['supplier_id'] != $user['id']) {
            return redirect()->to('/dashboard/supplier/products')->with('error', 'Product not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $contentToCheck = $this->request->getPost('name') . ' ' . $this->request->getPost('description') . ' ' . $this->request->getPost('specifications');
            $restrictedWord = check_restricted_keywords($contentToCheck);
            if ($restrictedWord !== false) {
                return redirect()->back()->withInput()->with('error', 'Your product contains a restricted keyword: "' . $restrictedWord . '". Please remove it and try again.');
            }

            $data = [
                'category_id' => $this->request->getPost('category_id'),
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'specifications' => $this->request->getPost('specifications'),
                'price_range' => $this->request->getPost('price_range'),
                'min_order_quantity' => $this->request->getPost('min_order_quantity'),
                'min_order_unit' => $this->request->getPost('min_order_unit'),
                'supply_ability' => $this->request->getPost('supply_ability'),
                'delivery_time' => $this->request->getPost('delivery_time'),
                'packaging' => $this->request->getPost('packaging'),
                'port' => $this->request->getPost('port'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'certifications' => $this->request->getPost('certifications'),
            ];

            $mainImage = $this->request->getFile('main_image');
            if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
                $newName = $mainImage->getRandomName();
                $mainImage->move('uploads/products', $newName);
                $data['main_image'] = $newName;
            }

            if ($this->productModel->update($id, $data)) {
                return redirect()->to('/dashboard/supplier/products')->with('success', 'Product updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update product.');
            }
        }

        return view('dashboard/supplier/product-form', [
            'title' => 'Edit Product',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'product' => $product,
        ]);
    }

    public function supplierDeleteProduct($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'supplier') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        $product = $this->productModel->find($id);

        if (!$product || $product['supplier_id'] != $user['id']) {
            return redirect()->to('/dashboard/supplier/products')->with('error', 'Product not found.');
        }

        if (!empty($product['main_image'])) {
            $imagePath = FCPATH . 'uploads/products/' . $product['main_image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->productModel->delete($id);
        return redirect()->to('/dashboard/supplier/products')->with('success', 'Product deleted successfully.');
    }

    public function supplierToggleProductStatus($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'supplier') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        $product = $this->productModel->find($id);

        if (!$product || $product['supplier_id'] != $user['id']) {
            return redirect()->to('/dashboard/supplier/products')->with('error', 'Product not found.');
        }

        $settingModelToggle = new SiteSettingModel();
        $autoApproveToggle = $settingModelToggle->getSetting('auto_approve_listings', '1');

        if ($product['status'] === 'pending' || ($autoApproveToggle !== '1' && $product['status'] === 'inactive')) {
            return redirect()->to('/dashboard/supplier/products')->with('error', 'This product is awaiting admin approval and cannot be modified right now.');
        }

        $newStatus = ($product['status'] === 'active') ? 'inactive' : 'active';
        $this->productModel->update($id, ['status' => $newStatus]);

        $msg = ($newStatus === 'active') ? 'Product listed successfully. It is now visible on the site.' : 'Product delisted. It is now hidden from the site.';
        return redirect()->to('/dashboard/supplier/products')->with('success', $msg);
    }

    public function supplierEditProfile()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'supplier') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $supplier = $this->userModel->find($userId);

        if (!$supplier) {
            return redirect()->to('/dashboard/supplier')->with('error', 'Profile not found.');
        }

        $products = $this->productModel
            ->where('supplier_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if ($this->request->getMethod() === 'POST') {
            $contentToCheck = $this->request->getPost('selling_products') . ' ' . $this->request->getPost('company_introduction');
            $restrictedWord = check_restricted_keywords($contentToCheck);
            if ($restrictedWord !== false) {
                return redirect()->back()->withInput()->with('error', 'Your profile contains a restricted keyword: "' . $restrictedWord . '". Please remove it and try again.');
            }

            $data = [
                'selling_products'     => $this->request->getPost('selling_products'),
                'company_introduction' => $this->request->getPost('company_introduction'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ];

            $uploadPath = FCPATH . 'uploads/suppliers';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $maxLogoSize   = 500 * 1024;
            $maxBannerSize = 1 * 1024 * 1024;
            $allowedTypes  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            if ($this->request->getPost('remove_logo') === '1') {
                if (!empty($supplier['company_logo'])) {
                    $logoFile = $uploadPath . '/' . $supplier['company_logo'];
                    if (file_exists($logoFile)) unlink($logoFile);
                }
                $data['company_logo'] = null;
            } else {
                $logo = $this->request->getFile('company_logo');
                if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                    if ($logo->getSize() > $maxLogoSize) {
                        return redirect()->back()->withInput()->with('error', 'Company logo must be under 500 KB.');
                    }
                    if (!in_array($logo->getMimeType(), $allowedTypes)) {
                        return redirect()->back()->withInput()->with('error', 'Company logo must be a JPG, PNG, WebP, or GIF image.');
                    }
                    $newName = $logo->getRandomName();
                    $logo->move($uploadPath, $newName);
                    $data['company_logo'] = $newName;
                }
            }

            $bannerFields     = ['banner_image', 'banner_image_2', 'banner_image_3'];
            $bannerInputNames = ['banner_image', 'banner_image_2', 'banner_image_3'];
            $removeNames      = ['remove_banner', 'remove_banner_2', 'remove_banner_3'];

            for ($i = 0; $i < 3; $i++) {
                $field      = $bannerFields[$i];
                $inputName  = $bannerInputNames[$i];
                $removeName = $removeNames[$i];

                if ($this->request->getPost($removeName) === '1') {
                    if (!empty($supplier[$field])) {
                        $bannerFile = $uploadPath . '/' . $supplier[$field];
                        if (file_exists($bannerFile)) unlink($bannerFile);
                    }
                    $data[$field] = null;
                } else {
                    $banner = $this->request->getFile($inputName);
                    if ($banner && $banner->isValid() && !$banner->hasMoved()) {
                        if ($banner->getSize() > $maxBannerSize) {
                            return redirect()->back()->withInput()->with('error', 'Banner image ' . ($i + 1) . ' must be under 1 MB.');
                        }
                        if (!in_array($banner->getMimeType(), $allowedTypes)) {
                            return redirect()->back()->withInput()->with('error', 'Banner image ' . ($i + 1) . ' must be a JPG, PNG, WebP, or GIF image.');
                        }
                        $newName = $banner->getRandomName();
                        $banner->move($uploadPath, $newName);
                        $data[$field] = $newName;
                    }
                }
            }

            try {
                $this->userModel->setValidationRules([]);
                $this->userModel->update($userId, $data);
                return redirect()->to('/dashboard/supplier/profile/edit')->with('success', 'Profile updated successfully.');
            } catch (\Exception $e) {
                log_message('error', 'Supplier profile update failed for ID ' . $userId . ': ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Failed to update profile: ' . $e->getMessage());
            }
        }

        return view('dashboard/supplier/profile-edit', [
            'title'     => 'Edit My Profile',
            'user'      => $supplier,
            'supplier'  => $supplier,
            'countries' => $this->countryModel->getActiveCountries(),
            'products'  => $products,
        ]);
    }

    public function buyer()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'buyer') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $inquiryCount = $this->inquiryModel->where('user_id', $user['id'])->countAllResults();

        return view('dashboard/buyer/index', [
            'title' => 'Buyer Dashboard',
            'user' => $user,
            'inquiryCount' => $inquiryCount,
        ]);
    }

    public function buyerInquiries()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'buyer') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $inquiries = $this->inquiryModel->where('user_id', $user['id'])->orderBy('created_at', 'DESC')->findAll();

        foreach ($inquiries as &$inquiry) {
            $inquiry['category'] = $this->categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $this->countryModel->find($inquiry['country_id']);
        }

        return view('dashboard/buyer/inquiries', [
            'title' => 'My Inquiries',
            'user' => $user,
            'inquiries' => $inquiries,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ]);
    }

    public function buyerAddInquiry()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'buyer') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        $settingModelCheck = new SiteSettingModel();
        $maxInquiries = (int) $settingModelCheck->getSetting('max_inquiries_per_buyer', '0');
        if ($maxInquiries > 0) {
            $currentCount = $this->inquiryModel->where('user_id', $user['id'])->countAllResults();
            if ($currentCount >= $maxInquiries) {
                return redirect()->to('/dashboard/buyer/inquiries')->with('error', 'You have reached the maximum limit of ' . $maxInquiries . ' inquiries. Please contact admin to increase your limit.');
            }
        }

        if ($this->request->getMethod() === 'POST') {
            $contentToCheck = $this->request->getPost('title') . ' ' . $this->request->getPost('product_name') . ' ' . $this->request->getPost('description');
            $restrictedWord = check_restricted_keywords($contentToCheck);
            if ($restrictedWord !== false) {
                return redirect()->back()->withInput()->with('error', 'Your inquiry contains a restricted keyword: "' . $restrictedWord . '". Please remove it and try again.');
            }

            $data = [
                'user_id' => $user['id'],
                'title' => $this->request->getPost('title'),
                'product_name' => $this->request->getPost('product_name'),
                'category_id' => $this->request->getPost('category_id'),
                'country_id' => $this->request->getPost('country_id'),
                'quantity' => $this->request->getPost('quantity'),
                'unit' => $this->request->getPost('unit'),
                'target_price' => $this->request->getPost('target_price'),
                'description' => $this->request->getPost('description'),
                'shipping_terms' => $this->request->getPost('shipping_terms'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'destination_port' => $this->request->getPost('destination_port'),
                'validity_date' => $this->request->getPost('validity_date'),
                'buyer_name' => $user['name'],
                'buyer_email' => $user['email'],
                'buyer_phone' => ($user['phone_code'] ?? '') . ($user['phone'] ?? ''),
                'buyer_company' => $user['company_name'] ?? '',
                'is_featured' => 0,
                'status' => 'active',
            ];

            $settingModel = new SiteSettingModel();
            $autoApprove = $settingModel->getSetting('auto_approve_listings', '1');
            if ($autoApprove !== '1') {
                // 'pending' is the awaiting-approval state and is the value the
                // product path uses for this same condition (see supplierAddProduct).
                // 'inactive' means "an admin deliberately hid this" and is set from
                // the admin listings screen instead.
                $data['status'] = 'pending';
            }

            $attachment = $this->request->getFile('attachment');
            if ($attachment && $attachment->isValid() && !$attachment->hasMoved()) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($attachment->getMimeType(), $allowedTypes)) {
                    return redirect()->back()->withInput()->with('error', 'Reference image must be a JPG, PNG, or WEBP file.');
                }
                if ($attachment->getSize() > 999 * 1024) {
                    return redirect()->back()->withInput()->with('error', 'Reference image must be less than 999 KB.');
                }
                $uploadPath = FCPATH . 'uploads/inquiries';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $newName = $attachment->getRandomName();
                $attachment->move($uploadPath, $newName);
                $data['attachment'] = $newName;
            }

            if ($this->inquiryModel->insert($data)) {
                helper('email');
                notifyAdminNewInquiry($data);
                $msg = $autoApprove === '1' ? 'Inquiry posted successfully.' : 'Inquiry submitted for review. It will be visible once approved by admin.';
                return redirect()->to('/dashboard/buyer/inquiries')->with('success', $msg);
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to post inquiry.');
            }
        }

        return view('dashboard/buyer/inquiry-form', [
            'title' => 'Post New Inquiry',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'inquiry' => null,
        ]);
    }

    public function buyerEditInquiry($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'buyer') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $inquiry = $this->inquiryModel->find($id);

        if (!$inquiry || $inquiry['user_id'] != $user['id']) {
            return redirect()->to('/dashboard/buyer/inquiries')->with('error', 'Inquiry not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $contentToCheck = $this->request->getPost('title') . ' ' . $this->request->getPost('product_name') . ' ' . $this->request->getPost('description');
            $restrictedWord = check_restricted_keywords($contentToCheck);
            if ($restrictedWord !== false) {
                return redirect()->back()->withInput()->with('error', 'Your inquiry contains a restricted keyword: "' . $restrictedWord . '". Please remove it and try again.');
            }

            $data = [
                'title' => $this->request->getPost('title'),
                'product_name' => $this->request->getPost('product_name'),
                'category_id' => $this->request->getPost('category_id'),
                'country_id' => $this->request->getPost('country_id'),
                'quantity' => $this->request->getPost('quantity'),
                'unit' => $this->request->getPost('unit'),
                'target_price' => $this->request->getPost('target_price'),
                'description' => $this->request->getPost('description'),
                'shipping_terms' => $this->request->getPost('shipping_terms'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'destination_port' => $this->request->getPost('destination_port'),
                'validity_date' => $this->request->getPost('validity_date'),
                'buyer_name' => $user['name'],
                'buyer_email' => $user['email'],
                'buyer_phone' => ($user['phone_code'] ?? '') . ($user['phone'] ?? ''),
                'buyer_company' => $user['company_name'] ?? '',
            ];

            $attachment = $this->request->getFile('attachment');
            if ($attachment && $attachment->isValid() && !$attachment->hasMoved()) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($attachment->getMimeType(), $allowedTypes)) {
                    return redirect()->back()->withInput()->with('error', 'Reference image must be a JPG, PNG, or WEBP file.');
                }
                if ($attachment->getSize() > 999 * 1024) {
                    return redirect()->back()->withInput()->with('error', 'Reference image must be less than 999 KB.');
                }
                if (!empty($inquiry['attachment'])) {
                    $oldPath = FCPATH . 'uploads/inquiries/' . $inquiry['attachment'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $uploadPath = FCPATH . 'uploads/inquiries';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $newName = $attachment->getRandomName();
                $attachment->move($uploadPath, $newName);
                $data['attachment'] = $newName;
            }

            if ($this->inquiryModel->update($id, $data)) {
                return redirect()->to('/dashboard/buyer/inquiries')->with('success', 'Inquiry updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update inquiry.');
            }
        }

        return view('dashboard/buyer/inquiry-form', [
            'title' => 'Edit Inquiry',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'inquiry' => $inquiry,
        ]);
    }

    public function buyerDeleteInquiry($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'buyer') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $inquiry = $this->inquiryModel->find($id);

        if (!$inquiry || $inquiry['user_id'] != $user['id']) {
            return redirect()->to('/dashboard/buyer/inquiries')->with('error', 'Inquiry not found.');
        }

        $this->inquiryModel->delete($id);
        return redirect()->to('/dashboard/buyer/inquiries')->with('success', 'Inquiry deleted successfully.');
    }

    public function approveUser($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($id);

        if (!$user || $user['user_type'] === 'admin') {
            return redirect()->back()->with('error', 'User not found.');
        }

        $this->userModel->update($id, ['status' => 'approved']);

        return redirect()->back()->with('success', 'User has been approved successfully.');
    }

    public function rejectUser($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($id);

        if (!$user || $user['user_type'] === 'admin') {
            return redirect()->back()->with('error', 'User not found.');
        }

        $this->userModel->update($id, ['status' => 'rejected']);

        return redirect()->back()->with('success', 'User has been rejected.');
    }

    public function deleteUser($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($id);

        if (!$user || $user['user_type'] === 'admin') {
            return redirect()->back()->with('error', 'User not found or cannot be deleted.');
        }

        $this->userModel->delete($id);

        return redirect()->back()->with('success', 'User has been deleted successfully.');
    }

    /**
     * Columns the Manage Suppliers table can be sorted by. Everything except
     * `country_name` is a real column on `users` (sorted in SQL); country
     * needs a join since it's only stored as `country_id` here.
     */
    private const SUPPLIER_SORT_FIELDS = ['uid', 'company_name', 'email', 'country_name', 'membership_level', 'status', 'is_featured', 'created_at'];

    public function suppliers()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $sort = (string) $this->request->getGet('sort');
        if (!in_array($sort, self::SUPPLIER_SORT_FIELDS, true)) {
            $sort = 'created_at';
        }
        $dir = strtolower((string) $this->request->getGet('dir')) === 'asc' ? 'asc' : 'desc';
        // Every redirect below reuses this so toggling a star or changing a
        // set never resets the admin's current column sort.
        $redirectUrl = '/dashboard/suppliers?sort=' . $sort . '&dir=' . $dir;

        $settingModel = new SiteSettingModel();

        if ($this->request->getMethod() === 'POST') {
            $action = $this->request->getPost('action');

            if ($action === 'toggle_featured_supplier') {
                // Plain flag toggle -- no "which carousel set" to assign
                // (Top Suppliers no longer pins from is_featured as of
                // 2026-08-23). Whether this flag does anything on the
                // homepage's separate "Featured Suppliers" section depends
                // on the "Show Starred Supplier as Featured" setting below.
                $id = $this->request->getPost('id');
                $supplier = $this->userModel->find($id);
                if ($supplier) {
                    $newVal = $supplier['is_featured'] ? 0 : 1;
                    $this->userModel->update($id, ['is_featured' => $newVal]);
                    $this->session->setFlashdata('success', 'Supplier featured status toggled.');
                }
            } elseif ($action === 'toggle_show_starred_as_featured') {
                $newVal = $this->request->getPost('enabled') ? '1' : '0';
                $settingModel->setSetting('show_starred_suppliers_as_featured', $newVal, 'supplier_display');
                $this->session->setFlashdata('success', 'Featured Suppliers display setting updated.');
            }

            return redirect()->to($redirectUrl);
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        if ($sort === 'country_name') {
            // Country data lives in app/Data/countries.php now, not a DB
            // table (see CountryModel), so this can no longer sort via a SQL
            // JOIN. Fetch every supplier, enrich + sort in PHP, then
            // paginate manually -- fine at this app's scale (a few hundred
            // suppliers). Pager::store() seeds a real Pager object from
            // scratch (the same primitive Model::paginate() uses
            // internally), so the view's existing $pager->links(...) call
            // keeps working unchanged.
            $allSuppliers = $this->userModel->where('user_type', 'supplier')->findAll();
            foreach ($allSuppliers as &$supplier) {
                $supplier['country'] = $this->countryModel->find($supplier['country_id']);
                $supplier['country_name'] = $supplier['country']['name'] ?? '';
            }
            unset($supplier);

            usort($allSuppliers, static function ($a, $b) use ($dir) {
                $cmp = strcasecmp($a['country_name'], $b['country_name']);
                return $dir === 'asc' ? $cmp : -$cmp;
            });

            $perPage = 25;
            $pagerService = service('pager');
            $currentPage = $pagerService->getCurrentPage('supplier');
            $pager = $pagerService->store('supplier', $currentPage, $perPage, count($allSuppliers));
            $offset = ($pagerService->getCurrentPage('supplier') - 1) * $perPage;
            $suppliers = array_slice($allSuppliers, $offset, $perPage);
        } else {
            $suppliers = $this->userModel
                ->where('user_type', 'supplier')
                ->orderBy($sort, $dir)
                ->paginate(25, 'supplier');
            $pager = $this->userModel->pager;

            foreach ($suppliers as &$supplier) {
                $supplier['country'] = $this->countryModel->find($supplier['country_id']);
            }
            unset($supplier);
        }

        return view('dashboard/admin/suppliers', [
            'title' => 'Manage Suppliers',
            'user' => $user,
            'suppliers' => $suppliers,
            'pager' => $pager,
            'sort' => $sort,
            'dir' => $dir,
            'showStarredAsFeatured' => $settingModel->getSetting('show_starred_suppliers_as_featured', '0') === '1',
        ]);
    }

    public function addSupplier()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $existingUser = $this->userModel->where('email', $email)->first();
            if ($existingUser) {
                return redirect()->back()->withInput()->with('error', 'A user with this email already exists.');
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $email,
                'password' => bin2hex(random_bytes(16)),
                'phone' => $this->request->getPost('phone'),
                'country_id' => $this->request->getPost('country_id'),
                'user_type' => $this->request->getPost('user_type') ?: 'supplier',
                'company_name' => $this->request->getPost('company_name'),
                'selling_products' => $this->request->getPost('selling_products'),
                'website' => $this->request->getPost('website'),
                'city' => $this->request->getPost('city'),
                'membership_level' => $this->request->getPost('membership_level') ?: 'free',
                'company_introduction' => $this->request->getPost('company_introduction'),
                'lead_stage' => 'new',
                'lead_source' => 'admin_added',
                'status' => $this->request->getPost('status') ?: 'approved',
            ];

            $uploadPath = FCPATH . 'uploads/suppliers';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $maxLogoSize = 500 * 1024;
            $maxBannerSize = 1 * 1024 * 1024;
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            $logo = $this->request->getFile('company_logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                if ($logo->getSize() > $maxLogoSize) {
                    return redirect()->back()->withInput()->with('error', 'Company logo must be under 500 KB.');
                }
                if (!in_array($logo->getMimeType(), $allowedTypes)) {
                    return redirect()->back()->withInput()->with('error', 'Company logo must be a JPG, PNG, WebP, or GIF image.');
                }
                $newName = $logo->getRandomName();
                $logo->move($uploadPath, $newName);
                $data['company_logo'] = $newName;
            }

            $bannerInputNames = ['banner_image', 'banner_image_2', 'banner_image_3'];
            for ($i = 0; $i < 3; $i++) {
                $banner = $this->request->getFile($bannerInputNames[$i]);
                if ($banner && $banner->isValid() && !$banner->hasMoved()) {
                    if ($banner->getSize() > $maxBannerSize) {
                        return redirect()->back()->withInput()->with('error', 'Banner image ' . ($i + 1) . ' must be under 1 MB.');
                    }
                    if (!in_array($banner->getMimeType(), $allowedTypes)) {
                        return redirect()->back()->withInput()->with('error', 'Banner image ' . ($i + 1) . ' must be a JPG, PNG, WebP, or GIF image.');
                    }
                    $newName = $banner->getRandomName();
                    $banner->move($uploadPath, $newName);
                    $data[$bannerInputNames[$i]] = $newName;
                }
            }

            if ($this->userModel->insert($data)) {
                return redirect()->to('/dashboard/suppliers')->with('success', 'Supplier added successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to add supplier.');
            }
        }

        return view('dashboard/admin/supplier-form', [
            'title' => 'Add Supplier',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'supplier' => null,
        ]);
    }

    public function editSupplier($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $supplier = $this->userModel->find($id);

        if (!$supplier) {
            return redirect()->to('/dashboard/suppliers')->with('error', 'Supplier not found.');
        }

        $products = $this->productModel
            ->where('supplier_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $db = \Config\Database::connect();

            $existingUser = $db->table('users')->where('email', $email)->where('id !=', $id)->get()->getRowArray();
            if ($existingUser) {
                return redirect()->back()->withInput()->with('error', 'A user with this email already exists.');
            }

            $companyName = $this->request->getPost('company_name');
            $slugSource = !empty($companyName) ? $companyName : ($this->request->getPost('name') ?? '');
            $baseSlug = url_title($slugSource, '-', true);
            if (empty($baseSlug)) {
                $baseSlug = $supplier['slug'] ?? 'user-' . $id;
            }
            $slug = $baseSlug;
            $slugCounter = 2;
            while ($db->table('users')->where('slug', $slug)->where('id !=', $id)->countAllResults() > 0) {
                $slug = $baseSlug . '-' . $slugCounter;
                $slugCounter++;
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $email,
                'phone' => $this->request->getPost('phone'),
                'country_id' => $this->request->getPost('country_id'),
                'user_type' => $this->request->getPost('user_type') ?: $supplier['user_type'],
                'company_name' => $companyName,
                'slug' => $slug,
                'selling_products' => $this->request->getPost('selling_products'),
                'website' => $this->request->getPost('website'),
                'city' => $this->request->getPost('city'),
                'membership_level' => $this->request->getPost('membership_level') ?: 'free',
                'company_introduction' => $this->request->getPost('company_introduction'),
                'status' => $this->request->getPost('status') ?: 'approved',
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $uploadPath = FCPATH . 'uploads/suppliers';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $maxLogoSize = 500 * 1024;
            $maxBannerSize = 1 * 1024 * 1024;
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            if ($this->request->getPost('remove_logo') === '1') {
                if (!empty($supplier['company_logo'])) {
                    $logoFile = $uploadPath . '/' . $supplier['company_logo'];
                    if (file_exists($logoFile)) {
                        unlink($logoFile);
                    }
                }
                $data['company_logo'] = null;
            } else {
                $logo = $this->request->getFile('company_logo');
                if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                    if ($logo->getSize() > $maxLogoSize) {
                        return redirect()->back()->withInput()->with('error', 'Company logo must be under 500 KB.');
                    }
                    if (!in_array($logo->getMimeType(), $allowedTypes)) {
                        return redirect()->back()->withInput()->with('error', 'Company logo must be a JPG, PNG, WebP, or GIF image.');
                    }
                    $newName = $logo->getRandomName();
                    $logo->move($uploadPath, $newName);
                    $data['company_logo'] = $newName;
                }
            }

            $bannerFields = ['banner_image', 'banner_image_2', 'banner_image_3'];
            $bannerInputNames = ['banner_image', 'banner_image_2', 'banner_image_3'];
            $removeNames = ['remove_banner', 'remove_banner_2', 'remove_banner_3'];

            for ($i = 0; $i < 3; $i++) {
                $field = $bannerFields[$i];
                $inputName = $bannerInputNames[$i];
                $removeName = $removeNames[$i];

                if ($this->request->getPost($removeName) === '1') {
                    if (!empty($supplier[$field])) {
                        $bannerFile = $uploadPath . '/' . $supplier[$field];
                        if (file_exists($bannerFile)) {
                            unlink($bannerFile);
                        }
                    }
                    $data[$field] = null;
                } else {
                    $banner = $this->request->getFile($inputName);
                    if ($banner && $banner->isValid() && !$banner->hasMoved()) {
                        if ($banner->getSize() > $maxBannerSize) {
                            return redirect()->back()->withInput()->with('error', 'Banner image ' . ($i + 1) . ' must be under 1 MB.');
                        }
                        if (!in_array($banner->getMimeType(), $allowedTypes)) {
                            return redirect()->back()->withInput()->with('error', 'Banner image ' . ($i + 1) . ' must be a JPG, PNG, WebP, or GIF image.');
                        }
                        $newName = $banner->getRandomName();
                        $banner->move($uploadPath, $newName);
                        $data[$field] = $newName;
                    }
                }
            }

            try {
                $db->table('users')->where('id', $id)->update($data);
                return redirect()->to('/dashboard/suppliers/edit/' . $id)->with('success', 'Supplier updated successfully.');
            } catch (\Exception $e) {
                log_message('error', 'Supplier update failed for ID ' . $id . ': ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Failed to update supplier: ' . $e->getMessage());
            }
        }

        return view('dashboard/admin/supplier-form', [
            'title' => 'Edit Supplier',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'supplier' => $supplier,
            'products' => $products,
        ]);
    }

    public function deleteSupplier($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $supplier = $this->userModel->where('user_type', 'supplier')->where('id', $id)->first();

        if (!$supplier) {
            return redirect()->to('/dashboard/suppliers')->with('error', 'Supplier not found.');
        }

        $this->userModel->delete($id);
        return redirect()->to('/dashboard/suppliers')->with('success', 'Supplier deleted successfully.');
    }

    public function adminAddProductForSupplier($supplierId)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $supplier = $this->userModel->where('user_type', 'supplier')->where('id', $supplierId)->first();

        if (!$supplier) {
            return redirect()->to('/dashboard/suppliers')->with('error', 'Supplier not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'supplier_id' => $supplierId,
                'category_id' => $this->request->getPost('category_id'),
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'specifications' => $this->request->getPost('specifications'),
                'price_range' => $this->request->getPost('price_range'),
                'min_order_quantity' => $this->request->getPost('min_order_quantity'),
                'min_order_unit' => $this->request->getPost('min_order_unit'),
                'supply_ability' => $this->request->getPost('supply_ability'),
                'delivery_time' => $this->request->getPost('delivery_time'),
                'packaging' => $this->request->getPost('packaging'),
                'port' => $this->request->getPost('port'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'certifications' => $this->request->getPost('certifications'),
                'is_featured' => 0,
                'status' => 'active',
            ];

            $mainImage = $this->request->getFile('main_image');
            if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/products';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $newName = $mainImage->getRandomName();
                $mainImage->move($uploadPath, $newName);
                $data['main_image'] = $newName;
            }

            if ($this->productModel->insert($data)) {
                return redirect()->to('/dashboard/suppliers/edit/' . $supplierId)->with('success', 'Product added successfully for ' . esc($supplier['company_name'] ?? $supplier['name']) . '.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to add product.');
            }
        }

        return view('dashboard/supplier/product-form', [
            'title' => 'Add Product for ' . ($supplier['company_name'] ?? $supplier['name']),
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'product' => null,
            'adminMode' => true,
            'supplier' => $supplier,
        ]);
    }

    public function adminEditProductForSupplier($supplierId, $productId)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $supplier = $this->userModel->where('user_type', 'supplier')->where('id', $supplierId)->first();

        if (!$supplier) {
            return redirect()->to('/dashboard/suppliers')->with('error', 'Supplier not found.');
        }

        $product = $this->productModel->find($productId);

        if (!$product || (int)$product['supplier_id'] !== (int)$supplierId) {
            return redirect()->to('/dashboard/suppliers/edit/' . $supplierId)->with('error', 'Product not found for this supplier.');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'category_id' => $this->request->getPost('category_id'),
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description'),
                'specifications' => $this->request->getPost('specifications'),
                'price_range' => $this->request->getPost('price_range'),
                'min_order_quantity' => $this->request->getPost('min_order_quantity'),
                'min_order_unit' => $this->request->getPost('min_order_unit'),
                'supply_ability' => $this->request->getPost('supply_ability'),
                'delivery_time' => $this->request->getPost('delivery_time'),
                'packaging' => $this->request->getPost('packaging'),
                'port' => $this->request->getPost('port'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'certifications' => $this->request->getPost('certifications'),
            ];

            $mainImage = $this->request->getFile('main_image');
            if ($mainImage && $mainImage->isValid() && !$mainImage->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/products';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $newName = $mainImage->getRandomName();
                $mainImage->move($uploadPath, $newName);
                $data['main_image'] = $newName;
            }

            if ($this->productModel->update($productId, $data)) {
                return redirect()->to('/dashboard/suppliers/edit/' . $supplierId)->with('success', 'Product updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update product.');
            }
        }

        return view('dashboard/supplier/product-form', [
            'title' => 'Edit Product for ' . ($supplier['company_name'] ?? $supplier['name']),
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'product' => $product,
            'adminMode' => true,
            'supplier' => $supplier,
        ]);
    }

    public function inquiries()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $inquiries = $this->inquiryModel->orderBy('created_at', 'DESC')->findAll();
        $countries = $this->countryModel->getActiveCountries();

        $agents = $this->userModel->whereIn('user_type', ['admin', 'agent'])->findAll();
        $agentMap = [];
        foreach ($agents as $agent) {
            $agentMap[$agent['id']] = $agent['name'];
        }

        foreach ($inquiries as &$inquiry) {
            $inquiry['category'] = $this->categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $this->countryModel->find($inquiry['country_id']);
            $inquiry['agent_name'] = '-';
            if (!empty($inquiry['user_id'])) {
                $buyerUser = $this->userModel->find($inquiry['user_id']);
                if ($buyerUser && !empty($buyerUser['assigned_agent_id']) && isset($agentMap[$buyerUser['assigned_agent_id']])) {
                    $inquiry['agent_name'] = $agentMap[$buyerUser['assigned_agent_id']];
                }
            }
        }

        return view('dashboard/admin/inquiries', [
            'title' => 'Manage Buyer Inquiries',
            'user' => $user,
            'inquiries' => $inquiries,
            'countries' => $countries,
        ]);
    }

    public function addInquiry()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        if ($this->request->getMethod() === 'POST') {
            // No restricted-keyword/profanity check here -- this is an admin
            // manually logging a real inquiry (e.g. an industrial buyer's RFQ
            // that legitimately mentions "explosive" or "weapon"-adjacent
            // terms), not a public submission. The filter exists to screen
            // public-facing submissions; admin data entry is trusted. Same
            // reasoning as addSupplier()/editSupplier(), which never had this
            // check -- this used to be the one admin form that did, inconsistently.
            $data = [
                'title' => $this->request->getPost('title'),
                'product_name' => $this->request->getPost('product_name'),
                'category_id' => $this->request->getPost('category_id'),
                'quantity' => $this->request->getPost('quantity'),
                'unit' => $this->request->getPost('unit'),
                'target_price' => $this->request->getPost('target_price'),
                'description' => $this->request->getPost('description'),
                'shipping_terms' => $this->request->getPost('shipping_terms'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'destination_port' => $this->request->getPost('destination_port'),
                'inquiry_date' => $this->request->getPost('inquiry_date'),
                'buyer_name' => $this->request->getPost('buyer_name'),
                'buyer_company' => $this->request->getPost('buyer_company'),
                'buyer_email' => $this->request->getPost('buyer_email'),
                'buyer_phone' => $this->request->getPost('buyer_phone'),
                'buyer_whatsapp' => $this->request->getPost('buyer_whatsapp'),
                'country_id' => $this->request->getPost('country_id'),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'status' => $this->request->getPost('status') ?: 'active',
            ];

            $attachment = $this->request->getFile('attachment');
            if ($attachment && $attachment->isValid() && !$attachment->hasMoved()) {
                $maxSize = 1 * 1024 * 1024;
                if ($attachment->getSize() > $maxSize) {
                    return redirect()->back()->withInput()->with('error', 'Product reference image must be less than 1 MB.');
                }
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($attachment->getMimeType(), $allowedTypes)) {
                    return redirect()->back()->withInput()->with('error', 'Product reference image must be JPEG, PNG, or WebP.');
                }
                $newName = $attachment->getRandomName();
                $attachment->move(FCPATH . 'uploads/inquiries', $newName);
                $data['attachment'] = $newName;
            }

            $buyerEmail = trim($this->request->getPost('buyer_email') ?? '');
            $buyerUserId = null;
            if (!empty($buyerEmail)) {
                $existingBuyer = $this->userModel->where('email', $buyerEmail)->where('user_type', 'buyer')->first();
                if ($existingBuyer) {
                    $buyerUserId = $existingBuyer['id'];
                } else {
                    $emailTaken = $this->userModel->where('email', $buyerEmail)->first();
                    if (!$emailTaken) {
                        $buyerData = [
                            'name' => $this->request->getPost('buyer_name') ?? 'Buyer',
                            'email' => $buyerEmail,
                            'phone' => $this->request->getPost('buyer_phone'),
                            'company_name' => $this->request->getPost('buyer_company'),
                            'user_type' => 'buyer',
                            'country_id' => $this->request->getPost('country_id'),
                            'password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
                            'status' => 'approved',
                            'lead_stage' => 'new',
                            'lead_source' => 'buy_offer',
                        ];
                        $this->userModel->insert($buyerData);
                        $buyerUserId = $this->userModel->getInsertID();
                    }
                }
            }
            $data['user_id'] = $buyerUserId;

            if ($this->inquiryModel->insert($data)) {
                return redirect()->to('/dashboard/inquiries')->with('success', 'Inquiry added successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to add inquiry.');
            }
        }

        return view('dashboard/admin/inquiry-form', [
            'title' => 'Add Buyer Inquiry',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'inquiry' => null,
        ]);
    }

    public function editInquiry($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $inquiry = $this->inquiryModel->find($id);

        if (!$inquiry) {
            return redirect()->to('/dashboard/inquiries')->with('error', 'Inquiry not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            // No restricted-keyword/profanity check here -- see the matching
            // comment in addInquiry(). Admin data entry is trusted; the
            // filter is for public-facing submissions.

            $data = [
                'title' => $this->request->getPost('title'),
                'product_name' => $this->request->getPost('product_name'),
                'category_id' => $this->request->getPost('category_id'),
                'quantity' => $this->request->getPost('quantity'),
                'unit' => $this->request->getPost('unit'),
                'target_price' => $this->request->getPost('target_price'),
                'description' => $this->request->getPost('description'),
                'shipping_terms' => $this->request->getPost('shipping_terms'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'destination_port' => $this->request->getPost('destination_port'),
                'inquiry_date' => $this->request->getPost('inquiry_date'),
                'buyer_name' => $this->request->getPost('buyer_name'),
                'buyer_company' => $this->request->getPost('buyer_company'),
                'buyer_email' => $this->request->getPost('buyer_email'),
                'buyer_phone' => $this->request->getPost('buyer_phone'),
                'buyer_whatsapp' => $this->request->getPost('buyer_whatsapp'),
                'country_id' => $this->request->getPost('country_id'),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'status' => $this->request->getPost('status') ?: 'active',
            ];

            if ($this->request->getPost('remove_attachment')) {
                if (!empty($inquiry['attachment'])) {
                    $oldPath = FCPATH . 'uploads/inquiries/' . $inquiry['attachment'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['attachment'] = null;
            }

            $attachment = $this->request->getFile('attachment');
            if ($attachment && $attachment->isValid() && !$attachment->hasMoved()) {
                $maxSize = 1 * 1024 * 1024;
                if ($attachment->getSize() > $maxSize) {
                    return redirect()->back()->withInput()->with('error', 'Product reference image must be less than 1 MB.');
                }
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($attachment->getMimeType(), $allowedTypes)) {
                    return redirect()->back()->withInput()->with('error', 'Product reference image must be JPEG, PNG, or WebP.');
                }
                if (!empty($inquiry['attachment'])) {
                    $oldPath = FCPATH . 'uploads/inquiries/' . $inquiry['attachment'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $newName = $attachment->getRandomName();
                $attachment->move(FCPATH . 'uploads/inquiries', $newName);
                $data['attachment'] = $newName;
            }

            $buyerEmail = trim($this->request->getPost('buyer_email') ?? '');
            $buyerUserId = $inquiry['user_id'];
            if (!empty($buyerEmail)) {
                $existingBuyer = $this->userModel->where('email', $buyerEmail)->where('user_type', 'buyer')->first();
                if ($existingBuyer) {
                    $buyerUserId = $existingBuyer['id'];
                } elseif (empty($buyerUserId)) {
                    $emailTaken = $this->userModel->where('email', $buyerEmail)->first();
                    if (!$emailTaken) {
                        $buyerUserData = [
                            'name' => $this->request->getPost('buyer_name') ?? 'Buyer',
                            'email' => $buyerEmail,
                            'phone' => $this->request->getPost('buyer_phone'),
                            'company_name' => $this->request->getPost('buyer_company'),
                            'user_type' => 'buyer',
                            'password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
                            'status' => 'approved',
                            'lead_stage' => 'new',
                            'lead_source' => 'buy_offer',
                        ];
                        $this->userModel->insert($buyerUserData);
                        $buyerUserId = $this->userModel->getInsertID();
                    }
                }
            }
            $data['user_id'] = $buyerUserId;

            if ($this->inquiryModel->update($id, $data)) {
                return redirect()->to('/dashboard/inquiries')->with('success', 'Inquiry updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update inquiry.');
            }
        }

        return view('dashboard/admin/inquiry-form', [
            'title' => 'Edit Buyer Inquiry',
            'user' => $user,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'inquiry' => $inquiry,
        ]);
    }

    public function deleteInquiry($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $inquiry = $this->inquiryModel->find($id);

        if (!$inquiry) {
            return redirect()->to('/dashboard/inquiries')->with('error', 'Inquiry not found.');
        }

        $this->inquiryModel->delete($id);
        return redirect()->to('/dashboard/inquiries')->with('success', 'Inquiry deleted successfully.');
    }

    public function profile()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $country = null;
        if (!empty($user['country_id'])) {
            $country = $this->countryModel->find($user['country_id']);
        }

        return view('dashboard/profile', [
            'title' => 'My Profile',
            'user' => $user,
            'country' => $country,
            'countries' => $this->countryModel->getActiveCountries(),
        ]);
    }

    public function updateProfile()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login');
        }

        $name = trim($this->request->getPost('name') ?? '');
        if (empty($name) || strlen($name) < 2) {
            return redirect()->back()->withInput()->with('error', 'Name is required and must be at least 2 characters.');
        }

        $data = [
            'name' => $name,
            'phone' => $this->request->getPost('phone'),
            'phone_code' => $this->request->getPost('phone_code'),
            'whatsapp' => $this->request->getPost('whatsapp') ? 1 : 0,
            'country_id' => $this->request->getPost('country_id'),
            'company_name' => $this->request->getPost('company_name'),
            'city' => $this->request->getPost('city'),
            'website' => $this->request->getPost('website'),
        ];

        if ($user['user_type'] === 'supplier') {
            $data['selling_products'] = $this->request->getPost('selling_products');
        } elseif ($user['user_type'] === 'buyer') {
            $data['buying_products'] = $this->request->getPost('buying_products');
            $data['requirement'] = $this->request->getPost('requirement');
        }

        $contentToCheck = $name . ' ' . $this->request->getPost('company_name') . ' '
            . ($data['selling_products'] ?? '') . ' ' . ($data['buying_products'] ?? '') . ' ' . ($data['requirement'] ?? '');
        $restrictedWord = check_restricted_keywords($contentToCheck);
        if ($restrictedWord !== false) {
            return redirect()->back()->withInput()->with('error', 'Your profile contains a restricted keyword: "' . $restrictedWord . '". Please remove it and try again.');
        }

        $newEmail = trim($this->request->getPost('email') ?? '');
        if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Please enter a valid email address.');
        }
        if ($newEmail !== $user['email']) {
            $existing = $this->userModel->where('email', $newEmail)->where('id !=', $userId)->first();
            if ($existing) {
                return redirect()->back()->withInput()->with('error', 'This email is already in use by another account.');
            }
            $data['email'] = $newEmail;
        }

        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                return redirect()->back()->withInput()->with('error', 'Password must be at least 6 characters.');
            }
            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->withInput()->with('error', 'Passwords do not match.');
            }
            $data['password'] = $newPassword;
        }

        $this->userModel->setValidationRules([]);
        if ($this->userModel->update($userId, $data)) {
            $this->session->set('user_name', $data['name']);
            return redirect()->to('/dashboard/profile')->with('success', 'Profile updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Read-only browse of the country/phone-code list -- see DECISIONS #15.
     * Data comes from app/Data/countries.php (CountryModel), not the DB;
     * "last updated" is that file's own mtime, which is exact regardless of
     * whether the most recent sync ran from here or from a cron'd
     * `php spark countries:sync`.
     */
    public function countries()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $countries = $this->countryModel->findAll();
        usort($countries, static fn ($a, $b) => strcmp($a['name'], $b['name']));

        $dataFile = APPPATH . 'Data/countries.php';
        $lastUpdated = is_file($dataFile) ? date('F j, Y \a\t g:i A', filemtime($dataFile)) : null;

        $user = $this->userModel->find($this->session->get('user_id'));

        return view('dashboard/admin/countries', [
            'title' => 'Countries - Admin',
            'user' => $user,
            'countries' => $countries,
            'lastUpdated' => $lastUpdated,
        ]);
    }

    /**
     * "Update Now" on the Countries page. Runs the same CountrySyncer as
     * `php spark countries:sync` -- see that class for the full sync logic.
     */
    public function syncCountriesNow()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/dashboard/countries');
        }

        $result = (new \App\Libraries\CountrySyncer())->sync();

        $this->session->setFlashdata($result['success'] ? 'success' : 'error', $result['message']);
        return redirect()->to('/dashboard/countries');
    }

    /**
     * "Site Security" admin page. For now, just lets an admin override "N"
     * (the capacity) for each rate-limited public form -- see
     * RateLimitFilter::forms() for the single source of truth on which
     * forms exist, their fixed time window, and default N. Overrides are
     * read from app/Data/rate_limits.php (a plain PHP file, like
     * app/Data/countries.php), not the database -- RateLimitFilter runs on
     * every hit to a public form, including ones a brute-force attempt
     * makes on purpose to get blocked, so this can't be a query that would
     * hit the DB on every single attempt.
     */
    public function siteSecurity()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $overrides = \App\Filters\RateLimitFilter::overrides();
        $forms = \App\Filters\RateLimitFilter::forms();

        foreach ($forms as $name => &$form) {
            $form['capacity'] = (isset($overrides[$name]) && (int) $overrides[$name] > 0)
                ? (int) $overrides[$name]
                : $form['default'];
        }
        unset($form);

        $user = $this->userModel->find($this->session->get('user_id'));

        return view('dashboard/admin/security', [
            'title' => 'Site Security - Admin',
            'user' => $user,
            'forms' => $forms,
        ]);
    }

    public function updateSiteSecurity()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/dashboard/security');
        }

        $forms = \App\Filters\RateLimitFilter::forms();
        $overrides = [];

        foreach ($forms as $name => $form) {
            $value = (int) $this->request->getPost('ratelimit_' . $name);
            if ($value < 1) {
                $value = 1;
            } elseif ($value > 1000) {
                $value = 1000;
            }
            $overrides[$name] = $value;
        }

        \App\Filters\RateLimitFilter::saveOverrides($overrides);

        $this->session->setFlashdata('success', 'Rate limiting settings updated.');
        return redirect()->to('/dashboard/security');
    }

    public function submissions()
    {
        if (!$this->session->get('logged_in') || !in_array($this->session->get('user_type'), ['admin', 'agent'])) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $submissionModel = new \App\Models\ContactSubmissionModel();

        $status = $this->request->getGet('status');
        $formType = $this->request->getGet('form_type');

        $builder = $submissionModel;

        if (!empty($status)) {
            $builder = $builder->where('status', $status);
        }
        if (!empty($formType)) {
            $builder = $builder->where('form_type', $formType);
        }

        $submissions = $builder->orderBy('created_at', 'DESC')->findAll();

        return view('dashboard/admin/submissions', [
            'title' => 'Form Submissions',
            'user' => $user,
            'submissions' => $submissions,
            'filters' => [
                'status' => $status,
                'form_type' => $formType,
            ],
        ]);
    }

    public function viewSubmission($id)
    {
        if (!$this->session->get('logged_in') || !in_array($this->session->get('user_type'), ['admin', 'agent'])) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $submissionModel = new \App\Models\ContactSubmissionModel();
        $countryModel = new \App\Models\CountryModel();
        $submission = $submissionModel->find($id);

        if (!$submission) {
            return redirect()->to('/dashboard/submissions')->with('error', 'Submission not found.');
        }

        if ($submission['status'] === 'new') {
            $submissionModel->update($id, ['status' => 'read']);
        }

        $country = null;
        if (!empty($submission['country_id'])) {
            $country = $countryModel->find($submission['country_id']);
        }

        return view('dashboard/admin/submission-detail', [
            'title' => 'Submission Detail',
            'user' => $user,
            'submission' => $submission,
            'country' => $country,
        ]);
    }

    public function updateSubmissionStatus($id)
    {
        if (!$this->session->get('logged_in') || !in_array($this->session->get('user_type'), ['admin', 'agent'])) {
            return redirect()->to('/login');
        }

        $submissionModel = new \App\Models\ContactSubmissionModel();
        $submission = $submissionModel->find($id);

        if (!$submission) {
            return redirect()->to('/dashboard/submissions')->with('error', 'Submission not found.');
        }

        $newStatus = $this->request->getPost('status');
        if (in_array($newStatus, ['new', 'read', 'replied', 'closed'])) {
            $submissionModel->update($id, ['status' => $newStatus]);
            return redirect()->back()->with('success', 'Status updated successfully.');
        }

        return redirect()->back()->with('error', 'Invalid status.');
    }

    public function deleteSubmission($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $submissionModel = new \App\Models\ContactSubmissionModel();
        $submission = $submissionModel->find($id);

        if (!$submission) {
            return redirect()->to('/dashboard/submissions')->with('error', 'Submission not found.');
        }

        $submissionModel->delete($id);
        return redirect()->to('/dashboard/submissions')->with('success', 'Submission deleted successfully.');
    }

    public function agents()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $db = \Config\Database::connect();
        
        $agents = $db->query("SELECT u.*, (SELECT COUNT(*) FROM users WHERE assigned_agent_id = u.id) as lead_count FROM users u WHERE u.user_type = 'agent' ORDER BY u.created_at DESC")->getResultArray();

        $lastAgent = $db->query("SELECT uid FROM users WHERE user_type = 'agent' ORDER BY id DESC LIMIT 1")->getRowArray();
        if ($lastAgent) {
            $lastNum = (int)preg_replace('/[^0-9]/', '', $lastAgent['uid']);
            $nextId = 'A-' . str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextId = 'A-000001';
        }

        return view('dashboard/admin/agents', [
            'title' => 'Manage Agents',
            'user' => $user,
            'agents' => $agents,
            'next_agent_id' => $nextId,
        ]);
    }

    public function addAgent()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/dashboard/agents');
        }

        $name = trim($this->request->getPost('name') ?? '');
        $email = trim($this->request->getPost('email') ?? '');
        $password = $this->request->getPost('password');
        $department = $this->request->getPost('department');

        if (empty($name) || empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'Name, email, and password are required.');
        }

        $existing = $this->userModel->where('email', $email)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'This email is already registered.');
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'user_type' => 'agent',
            'phone' => $this->request->getPost('phone'),
            'phone_code' => $this->request->getPost('phone_code'),
            'department' => $department,
            'status' => 'approved',
            'membership_level' => 'free',
            'lead_stage' => 'new',
        ];

        $this->userModel->setValidationRules([
            'name' => 'required|min_length[2]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ]);

        if ($this->userModel->insert($data)) {
            return redirect()->to('/dashboard/agents')->with('success', 'Agent created successfully.');
        } else {
            $errors = $this->userModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors));
        }
    }

    public function editAgent($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $agent = $this->userModel->find($id);
        if (!$agent || $agent['user_type'] !== 'agent') {
            return redirect()->to('/dashboard/agents')->with('error', 'Agent not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $name = trim($this->request->getPost('name') ?? '');
            $email = trim($this->request->getPost('email') ?? '');

            if (empty($name) || empty($email)) {
                return redirect()->back()->with('error', 'Name and email are required.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Please enter a valid email address.');
            }

            if ($email !== $agent['email']) {
                $existing = $this->userModel->where('email', $email)->where('id !=', $id)->first();
                if ($existing) {
                    return redirect()->back()->with('error', 'This email is already in use.');
                }
            }

            $data = [
                'name' => $name,
                'email' => $email,
                'phone' => $this->request->getPost('phone'),
                'phone_code' => $this->request->getPost('phone_code'),
                'department' => $this->request->getPost('department'),
                'status' => $this->request->getPost('status'),
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    return redirect()->back()->with('error', 'Password must be at least 6 characters.');
                }
                $data['password'] = $password;
            }

            $this->userModel->setValidationRules([]);
            if ($this->userModel->update($id, $data)) {
                return redirect()->to('/dashboard/agents')->with('success', 'Agent updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to update agent.');
            }
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $db = \Config\Database::connect();
        $agents = $db->query("SELECT u.*, (SELECT COUNT(*) FROM users WHERE assigned_agent_id = u.id) as lead_count FROM users u WHERE u.user_type = 'agent' ORDER BY u.created_at DESC")->getResultArray();

        return view('dashboard/admin/agents', [
            'title' => 'Edit Agent',
            'user' => $user,
            'agents' => $agents,
            'edit_agent' => $agent,
            'next_agent_id' => $agent['uid'],
        ]);
    }

    public function deleteAgent($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $agent = $this->userModel->find($id);
        if (!$agent || $agent['user_type'] !== 'agent') {
            return redirect()->to('/dashboard/agents')->with('error', 'Agent not found.');
        }

        $db = \Config\Database::connect();
        $db->table('users')->where('assigned_agent_id', $id)->update(['assigned_agent_id' => null]);

        $this->userModel->delete($id);
        return redirect()->to('/dashboard/agents')->with('success', 'Agent deleted successfully.');
    }

    public function adminEditUser($id)
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $targetUser = $this->userModel->find($id);

        if (!$targetUser) {
            return redirect()->to('/dashboard/users')->with('error', 'User not found.');
        }

        $countries = $this->countryModel->getActiveCountries();

        if ($this->request->getMethod() === 'POST') {
            $name = trim($this->request->getPost('name') ?? '');
            if (empty($name) || strlen($name) < 2) {
                return redirect()->back()->withInput()->with('error', 'Name is required and must be at least 2 characters.');
            }

            $data = [
                'name' => $name,
                'phone' => $this->request->getPost('phone'),
                'phone_code' => $this->request->getPost('phone_code'),
                'whatsapp' => $this->request->getPost('whatsapp') ? 1 : 0,
                'country_id' => $this->request->getPost('country_id'),
                'company_name' => $this->request->getPost('company_name'),
                'city' => $this->request->getPost('city'),
                'website' => $this->request->getPost('website'),
                'status' => $this->request->getPost('status'),
                'membership_level' => $this->request->getPost('membership_level'),
                'lead_stage' => $this->request->getPost('lead_stage'),
                'lead_source' => $this->request->getPost('lead_source'),
                'assigned_agent_id' => $this->request->getPost('assigned_agent_id') ?: null,
            ];

            if ($targetUser['user_type'] === 'supplier') {
                $data['selling_products'] = $this->request->getPost('selling_products');
            } elseif ($targetUser['user_type'] === 'buyer') {
                $data['buying_products'] = $this->request->getPost('buying_products');
                $data['requirement'] = $this->request->getPost('requirement');
            }

            if ($targetUser['user_type'] === 'agent') {
                $data['department'] = $this->request->getPost('department');
            }

            $newEmail = trim($this->request->getPost('email') ?? '');
            if (!empty($newEmail) && filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                if ($newEmail !== $targetUser['email']) {
                    $existing = $this->userModel->where('email', $newEmail)->where('id !=', $id)->first();
                    if ($existing) {
                        return redirect()->back()->withInput()->with('error', 'This email is already in use by another account.');
                    }
                    $data['email'] = $newEmail;
                }
            }

            $newPassword = $this->request->getPost('new_password');
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    return redirect()->back()->withInput()->with('error', 'Password must be at least 6 characters.');
                }
                $data['password'] = $newPassword;
            }

            $this->userModel->setValidationRules([]);
            if ($this->userModel->update($id, $data)) {
                return redirect()->to('/dashboard/admin-edit-user/' . $id)->with('success', 'User profile updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update profile.');
            }
        }

        $agents = $this->userModel->getAgents();
        $leadStages = $this->userModel->getLeadStages();
        $membershipLevels = $this->userModel->getMembershipLevels();

        return view('dashboard/admin/edit-user', [
            'title' => 'Edit User - ' . $targetUser['name'],
            'user' => $user,
            'target_user' => $targetUser,
            'countries' => $countries,
            'agents' => $agents,
            'lead_stages' => $leadStages,
            'membership_levels' => $membershipLevels,
        ]);
    }
}
