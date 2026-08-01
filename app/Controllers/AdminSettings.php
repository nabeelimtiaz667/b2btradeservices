<?php

namespace App\Controllers;

use App\Models\SiteSettingModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;
use App\Models\UserModel;

class AdminSettings extends BaseController
{
    protected $settingModel;
    protected $categoryModel;
    protected $productModel;
    protected $inquiryModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->settingModel = new SiteSettingModel();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
        $this->inquiryModel = new BuyerInquiryModel();
        $this->userModel = new UserModel();
        $this->session = session();
    }

    private function checkAdmin()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return false;
        }
        return true;
    }

    public function index()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }
        return redirect()->to('/admin/settings/general');
    }

    public function general()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $textKeys = ['site_name', 'site_tagline', 'contact_email', 'contact_phone', 'contact_address', 'footer_text'];
            $toggleKeys = ['maintenance_mode'];
            foreach ($textKeys as $key) {
                $value = $this->request->getPost($key) ?? '';
                $this->settingModel->setSetting($key, $value, 'general');
            }
            foreach ($toggleKeys as $key) {
                $value = $this->request->getPost($key) ? '1' : '0';
                $this->settingModel->setSetting($key, $value, 'general');
            }
            $this->session->setFlashdata('success', 'General settings saved successfully.');
            return redirect()->to('/admin/settings/general');
        }

        $settings = $this->settingModel->getSettingsByGroup('general');
        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/general', [
            'title' => 'General Settings - Admin',
            'user' => $user,
            'settings' => $settings,
            'activeTab' => 'general',
        ]);
    }

    public function seo()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $keys = ['meta_title', 'meta_description', 'meta_keywords', 'google_analytics_id', 'google_tag_manager_id'];
            foreach ($keys as $key) {
                $value = $this->request->getPost($key) ?? '';
                $this->settingModel->setSetting($key, $value, 'seo');
            }
            $this->session->setFlashdata('success', 'SEO settings saved successfully.');
            return redirect()->to('/admin/settings/seo');
        }

        $settings = $this->settingModel->getSettingsByGroup('seo');
        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/seo', [
            'title' => 'SEO Settings - Admin',
            'user' => $user,
            'settings' => $settings,
            'activeTab' => 'seo',
        ]);
    }

    public function moderation()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $textKeys = ['restricted_keywords'];
            $toggleKeys = ['auto_approve_listings', 'require_admin_review', 'profanity_filter'];

            foreach ($textKeys as $key) {
                $value = $this->request->getPost($key) ?? '';
                $this->settingModel->setSetting($key, $value, 'moderation');
            }
            foreach ($toggleKeys as $key) {
                $value = $this->request->getPost($key) ? '1' : '0';
                $this->settingModel->setSetting($key, $value, 'moderation');
            }

            $this->session->setFlashdata('success', 'Content moderation settings saved successfully.');
            return redirect()->to('/admin/settings/moderation');
        }

        $settings = $this->settingModel->getSettingsByGroup('moderation');
        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/moderation', [
            'title' => 'Content Moderation - Admin',
            'user' => $user,
            'settings' => $settings,
            'activeTab' => 'moderation',
        ]);
    }

    public function categories()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $action = $this->request->getPost('action');

            if ($action === 'add') {
                $name = $this->request->getPost('name');
                $slug = url_title($name, '-', true);
                $description = $this->request->getPost('description') ?? '';
                $status = $this->request->getPost('status') ?? 'active';

                $this->categoryModel->insert([
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'status' => $status,
                ]);
                $this->session->setFlashdata('success', 'Category added successfully.');
            } elseif ($action === 'update') {
                $id = $this->request->getPost('id');
                $name = $this->request->getPost('name');
                $description = $this->request->getPost('description') ?? '';
                $status = $this->request->getPost('status') ?? 'active';

                $this->categoryModel->update($id, [
                    'name' => $name,
                    'slug' => url_title($name, '-', true),
                    'description' => $description,
                    'status' => $status,
                ]);
                $this->session->setFlashdata('success', 'Category updated successfully.');
            } elseif ($action === 'delete') {
                $id = $this->request->getPost('id');
                $this->categoryModel->delete($id);
                $this->session->setFlashdata('success', 'Category deleted successfully.');
            }

            return redirect()->to('/admin/settings/categories');
        }

        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/categories', [
            'title' => 'Category Management - Admin',
            'user' => $user,
            'categories' => $categories,
            'activeTab' => 'categories',
        ]);
    }

    public function listings()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $action = $this->request->getPost('action');

            if ($action === 'update_product_status') {
                $id = $this->request->getPost('id');
                $status = $this->request->getPost('status');
                // Whitelisted against the column ENUM. sql_mode here has no STRICT
                // flag, so an unrecognised value would be silently coerced to ''
                // and the row would vanish from every listing.
                if (!in_array($status, ['active', 'inactive', 'pending'], true)) {
                    $this->session->setFlashdata('error', 'Invalid product status.');
                    return redirect()->back();
                }
                $this->productModel->update($id, ['status' => $status]);
                $this->session->setFlashdata('success', 'Product status updated.');
            } elseif ($action === 'update_inquiry_status') {
                $id = $this->request->getPost('id');
                $status = $this->request->getPost('status');
                if (!in_array($status, ['active', 'inactive', 'closed', 'pending', 'expired'], true)) {
                    $this->session->setFlashdata('error', 'Invalid inquiry status.');
                    return redirect()->back();
                }
                $this->inquiryModel->update($id, ['status' => $status]);
                $this->session->setFlashdata('success', 'Inquiry status updated.');
            } elseif ($action === 'delete_product') {
                $id = $this->request->getPost('id');
                $this->productModel->delete($id);
                $this->session->setFlashdata('success', 'Product deleted.');
            } elseif ($action === 'delete_inquiry') {
                $id = $this->request->getPost('id');
                $this->inquiryModel->delete($id);
                $this->session->setFlashdata('success', 'Inquiry deleted.');
            } elseif ($action === 'toggle_featured_product') {
                $id = $this->request->getPost('id');
                $product = $this->productModel->find($id);
                if ($product) {
                    $newVal = $product['is_featured'] ? 0 : 1;
                    $this->productModel->update($id, ['is_featured' => $newVal]);
                    $this->session->setFlashdata('success', 'Product featured status toggled.');
                }
            } elseif ($action === 'toggle_featured_inquiry') {
                $id = $this->request->getPost('id');
                $inquiry = $this->inquiryModel->find($id);
                if ($inquiry) {
                    $newVal = $inquiry['is_featured'] ? 0 : 1;
                    $this->inquiryModel->update($id, ['is_featured' => $newVal]);
                    $this->session->setFlashdata('success', 'Inquiry featured status toggled.');
                }
            }

            return redirect()->to('/admin/settings/listings');
        }

        $products = $this->productModel->orderBy('created_at', 'DESC')->findAll();
        foreach ($products as &$p) {
            if (!empty($p['supplier_id'])) {
                $supplier = $this->userModel->find($p['supplier_id']);
                $p['supplier_name'] = $supplier ? ($supplier['company_name'] ?? 'N/A') : 'N/A';
            } else {
                $p['supplier_name'] = 'N/A';
            }
            if (!empty($p['category_id'])) {
                $category = $this->categoryModel->find($p['category_id']);
                $p['category_name'] = $category ? ($category['name'] ?? 'N/A') : 'N/A';
            } else {
                $p['category_name'] = 'N/A';
            }
        }

        $inquiries = $this->inquiryModel->orderBy('created_at', 'DESC')->findAll();
        foreach ($inquiries as &$inq) {
            if (empty($inq['buyer_name']) && !empty($inq['user_id'])) {
                $buyer = $this->userModel->find($inq['user_id']);
                $inq['buyer_name'] = $buyer ? ($buyer['name'] ?? 'N/A') : 'N/A';
            }
            $inq['buyer_name'] = $inq['buyer_name'] ?? 'N/A';
            if (!empty($inq['category_id'])) {
                $category = $this->categoryModel->find($inq['category_id']);
                $inq['category_name'] = $category ? ($category['name'] ?? 'N/A') : 'N/A';
            } else {
                $inq['category_name'] = 'N/A';
            }
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/listings', [
            'title' => 'Listing Management - Admin',
            'user' => $user,
            'products' => $products,
            'inquiries' => $inquiries,
            'activeTab' => 'listings',
        ]);
    }

    public function registration()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $textKeys = ['default_user_status', 'max_products_per_supplier', 'max_inquiries_per_buyer'];
            $toggleKeys = ['allow_registration'];

            foreach ($textKeys as $key) {
                $value = $this->request->getPost($key) ?? '';
                $this->settingModel->setSetting($key, $value, 'registration');
            }
            foreach ($toggleKeys as $key) {
                $value = $this->request->getPost($key) ? '1' : '0';
                $this->settingModel->setSetting($key, $value, 'registration');
            }

            $this->session->setFlashdata('success', 'Registration settings saved successfully.');
            return redirect()->to('/admin/settings/registration');
        }

        $settings = $this->settingModel->getSettingsByGroup('registration');
        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/registration', [
            'title' => 'Registration Settings - Admin',
            'user' => $user,
            'settings' => $settings,
            'activeTab' => 'registration',
        ]);
    }

    public function email()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $textKeys = ['admin_notification_email', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass'];
            $toggleKeys = ['notify_on_registration', 'notify_on_new_listing', 'notify_on_inquiry'];

            foreach ($textKeys as $key) {
                $value = $this->request->getPost($key) ?? '';
                $this->settingModel->setSetting($key, $value, 'email');
            }
            foreach ($toggleKeys as $key) {
                $value = $this->request->getPost($key) ? '1' : '0';
                $this->settingModel->setSetting($key, $value, 'email');
            }

            $this->session->setFlashdata('success', 'Email settings saved successfully.');
            return redirect()->to('/admin/settings/email');
        }

        $settings = $this->settingModel->getSettingsByGroup('email');
        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/email', [
            'title' => 'Email Settings - Admin',
            'user' => $user,
            'settings' => $settings,
            'activeTab' => 'email',
        ]);
    }
}
