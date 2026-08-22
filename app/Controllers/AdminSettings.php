<?php

namespace App\Controllers;

use App\Models\SiteSettingModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;
use App\Models\UserModel;
use App\Models\HeroBannerSlideModel;

class AdminSettings extends BaseController
{
    /**
     * Mirrors Pages::TOP_PRODUCTS_DISPLAY_COUNT -- a carousel set can hold
     * at most this many pinned products (there's no per-set 1-pin cap,
     * admin picks the set directly, but a set can't hold more pins than it
     * has display slots). Must stay in sync with that constant and with
     * Dashboard::TOP_SUPPLIERS_ITEMS_PER_SET for the supplier side.
     */
    private const TOP_PRODUCTS_ITEMS_PER_SET = 3;

    /**
     * Loose shape/quality gate for hero banner uploads -- not an exact
     * pixel match (the display side now crops to a fixed box via CSS
     * object-fit, see .banner-slider-sec in style.css, so it doesn't need
     * one). Aspect ratio just has to be roughly landscape-banner-shaped:
     * the current live images sit at 1340/1020 ~= 1.31, so 1.2-1.6 gives
     * real tolerance on either side while still hard-rejecting a mobile
     * screenshot (portrait, ratio < 1) or a widescreen/16:9-shaped image
     * (1.78+) -- either would look wrong cropped into this banner's shape.
     * Minimum width/height is a pixelation floor, not a shape constraint:
     * 1200x750 is the smallest image that can still cover the display box
     * at 1.6 (the loosest allowed ratio) without upscaling.
     */
    private const HERO_BANNER_MIN_ASPECT_RATIO = 1.2;
    private const HERO_BANNER_MAX_ASPECT_RATIO = 1.6;
    private const HERO_BANNER_MIN_WIDTH = 1200;
    private const HERO_BANNER_MIN_HEIGHT = 750;
    private const HERO_BANNER_MAX_SIZE = 2 * 1024 * 1024;
    private const HERO_BANNER_ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    protected $settingModel;
    protected $categoryModel;
    protected $productModel;
    protected $inquiryModel;
    protected $userModel;
    protected $heroBannerModel;
    protected $session;

    public function __construct()
    {
        $this->settingModel = new SiteSettingModel();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
        $this->inquiryModel = new BuyerInquiryModel();
        $this->userModel = new UserModel();
        $this->heroBannerModel = new HeroBannerSlideModel();
        $this->session = session();
    }

    private function checkAdmin()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * True if $setNumber has room for one more pinned product, i.e. fewer
     * than TOP_PRODUCTS_ITEMS_PER_SET are already pinned into it (only
     * counting active products -- an inactive/pending one wouldn't actually
     * render there, matching Pages::index()'s own filter). $excludeProductId
     * lets an existing pin re-save into the same set without counting
     * itself against its own room.
     */
    private function productSetHasRoom(int $setNumber, ?int $excludeProductId = null): bool
    {
        $builder = $this->productModel
            ->where('status', 'active')
            ->where('is_featured', 1)
            ->where('featured_set', $setNumber);

        if ($excludeProductId) {
            $builder->where('id !=', $excludeProductId);
        }

        return $builder->countAllResults() < self::TOP_PRODUCTS_ITEMS_PER_SET;
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

    /**
     * Manually forces the sitemap cache to regenerate on next request, ahead
     * of Sitemap::CACHE_TTL's 7-day auto-refresh -- e.g. right after a bulk
     * import. Only Sitemap.php uses the cache service (verified), so a full
     * clean() is equivalent to the targeted deletes the CLI `cache:clear`
     * command already does for this same purpose, without needing to track
     * every paginated rfqs-N/suppliers-N cache key here too.
     */
    public function refreshSitemaps()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/settings/seo');
        }

        \Config\Services::cache()->clean();

        $this->session->setFlashdata('success', 'Sitemaps will regenerate from the live database on their next request.');
        return redirect()->to('/admin/settings/seo');
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

    /**
     * Columns the Listings tab's Products table can be sorted by. `id`,
     * `name`, `status`, `is_featured`, `created_at` are real columns (sorted
     * in SQL); `supplier_name`/`category_name` are attached after fetch (no
     * join), so those are sorted in PHP after enrichment -- see listings().
     */
    private const PRODUCT_SORT_FIELDS = ['id', 'name', 'status', 'is_featured', 'created_at', 'supplier_name', 'category_name'];

    public function listings()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        $sort = (string) $this->request->getGet('sort');
        if (!in_array($sort, self::PRODUCT_SORT_FIELDS, true)) {
            $sort = 'created_at';
        }
        $dir = strtolower((string) $this->request->getGet('dir')) === 'asc' ? 'asc' : 'desc';
        // Every redirect below this point (both actions and the final
        // fall-through) uses this so a sort/toggle/status-change never
        // resets the admin's current column sort.
        $redirectUrl = '/admin/settings/listings?sort=' . $sort . '&dir=' . $dir;

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
                    return redirect()->to($redirectUrl);
                }
                $this->productModel->update($id, ['status' => $status]);
                $this->session->setFlashdata('success', 'Product status updated.');
            } elseif ($action === 'update_inquiry_status') {
                $id = $this->request->getPost('id');
                $status = $this->request->getPost('status');
                if (!in_array($status, ['active', 'inactive', 'closed', 'pending', 'expired'], true)) {
                    $this->session->setFlashdata('error', 'Invalid inquiry status.');
                    return redirect()->to($redirectUrl);
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
                    if ($newVal) {
                        // Reuse the product's last-known set if it still has
                        // room; otherwise auto-pick the first set that does,
                        // rather than hard-defaulting to set 1 -- a product
                        // has no "Set N" dropdown until it's already
                        // featured, so hard-defaulting to set 1 would strand
                        // admins with no way to land a new pin anywhere once
                        // set 1 fills up.
                        $preferredSet = (int) ($product['featured_set'] ?: 0);
                        $targetSet = ($preferredSet >= 1 && $this->productSetHasRoom($preferredSet, $id))
                            ? $preferredSet
                            : null;

                        if ($targetSet === null) {
                            $setCount = max(1, min(10, (int) $this->settingModel->getSetting('top_products_set_count', 1)));
                            for ($s = 1; $s <= $setCount; $s++) {
                                if ($this->productSetHasRoom($s, $id)) {
                                    $targetSet = $s;
                                    break;
                                }
                            }
                        }

                        if ($targetSet === null) {
                            $this->session->setFlashdata('error',
                                'All ' . $setCount . ' carousel set(s) already have the maximum '
                                . self::TOP_PRODUCTS_ITEMS_PER_SET . ' pinned products each. Free up a set '
                                . 'first, or add more sets on the Top Sections tab.');
                            return redirect()->to($redirectUrl);
                        }

                        $this->productModel->update($id, ['is_featured' => 1, 'featured_set' => $targetSet]);
                    } else {
                        $this->productModel->update($id, ['is_featured' => 0]);
                    }
                    $this->session->setFlashdata('success', 'Product featured status toggled.');
                }
            } elseif ($action === 'set_product_featured_set') {
                $id = $this->request->getPost('id');
                $setNum = (int) $this->request->getPost('featured_set');
                if ($setNum >= 1 && $setNum <= 10) {
                    if (!$this->productSetHasRoom($setNum, $id)) {
                        $this->session->setFlashdata('error',
                            'Set ' . $setNum . ' already has the maximum ' . self::TOP_PRODUCTS_ITEMS_PER_SET . ' pinned products.');
                        return redirect()->to($redirectUrl);
                    }
                    $this->productModel->update($id, ['featured_set' => $setNum]);
                    $this->session->setFlashdata('success', 'Product carousel set updated.');
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

            return redirect()->to($redirectUrl);
        }

        // supplier_name/category_name aren't real columns (no join), so a
        // sort by either of those has to happen in PHP after enrichment
        // below; everything else sorts in SQL directly.
        $productNativeSortFields = ['id', 'name', 'status', 'is_featured', 'created_at'];
        $productsQuery = $this->productModel;
        $productsQuery = in_array($sort, $productNativeSortFields, true)
            ? $productsQuery->orderBy($sort, $dir)
            : $productsQuery->orderBy('created_at', 'DESC');
        $products = $productsQuery->findAll();

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
        unset($p);

        if (in_array($sort, ['supplier_name', 'category_name'], true)) {
            usort($products, function ($a, $b) use ($sort, $dir) {
                $cmp = strcasecmp($a[$sort], $b[$sort]);
                return $dir === 'asc' ? $cmp : -$cmp;
            });
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
        $productSetCount = max(1, min(10, (int) $this->settingModel->getSetting('top_products_set_count', 1)));

        return view('admin/settings/listings', [
            'title' => 'Listing Management - Admin',
            'user' => $user,
            'products' => $products,
            'inquiries' => $inquiries,
            'activeTab' => 'listings',
            'productSetCount' => $productSetCount,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    /**
     * Number of rotating sets and per-set display time (seconds) for the
     * homepage's Top Products / Top Suppliers carousels. Which specific
     * product/supplier is pinned into which set is set elsewhere (the
     * "Featured" column on the Listings tab for products, the "Featured
     * Supplier" checkbox on a supplier's edit page) -- this tab only
     * controls how many sets exist and how fast they rotate. See
     * Pages::index() for how a set count change reshapes the pools, and
     * CHANGELOG 2026-08-21.
     */
    public function topSections()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $fields = [
                'top_products_set_count' => ['min' => 1, 'max' => 10, 'default' => 1],
                'top_products_interval_seconds' => ['min' => 2, 'max' => 60, 'default' => 5],
                'top_suppliers_set_count' => ['min' => 1, 'max' => 10, 'default' => 1],
                'top_suppliers_interval_seconds' => ['min' => 2, 'max' => 60, 'default' => 5],
            ];

            foreach ($fields as $key => $bounds) {
                $value = (int) $this->request->getPost($key);
                $value = max($bounds['min'], min($bounds['max'], $value ?: $bounds['default']));
                $this->settingModel->setSetting($key, (string) $value, 'homepage_carousel');
            }

            $this->session->setFlashdata('success', 'Top Products/Top Suppliers carousel settings saved successfully.');
            return redirect()->to('/admin/settings/top-sections');
        }

        $settings = $this->settingModel->getSettingsByGroup('homepage_carousel');
        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/top-sections', [
            'title' => 'Top Products / Top Suppliers Carousel - Admin',
            'user' => $user,
            'settings' => $settings,
            'activeTab' => 'top-sections',
        ]);
    }

    /**
     * Admin CRM for the homepage's hero banner (`.banner-slider` in
     * index.php) -- add/edit/retire/restore/permanently-delete slides, no
     * cap on how many can exist. "Active" slides are what actually renders
     * on the homepage (Pages::index()); "history" is a retired shelf, not a
     * real delete -- files on disk only get removed on permanent delete.
     * See CHANGELOG 2026-08-22.
     */
    public function heroBanners()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        $uploadPath = FCPATH . 'uploads/hero-banner';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($this->request->getMethod() === 'POST') {
            $action = $this->request->getPost('action');

            if ($action === 'add') {
                $linkUrl = trim((string) $this->request->getPost('link_url'));
                if ($linkUrl === '') {
                    $this->session->setFlashdata('error', 'A hero banner slide needs a link.');
                    return redirect()->to('/admin/settings/hero-banners');
                }

                // Radio choice: exactly one of file-upload / image-URL is
                // enabled client-side (the other is disabled, so browsers
                // never submit it), but the server decides purely off
                // input_type -- never trusts which POST fields happen to be
                // present.
                $inputType = $this->request->getPost('input_type') === 'url' ? 'url' : 'upload';

                if ($inputType === 'url') {
                    // Used exactly as submitted -- no trim/sanitizing/
                    // reformatting, per explicit instruction.
                    $imageValue = (string) $this->request->getPost('image_url');
                    if ($imageValue === '') {
                        $this->session->setFlashdata('error', 'Enter an image URL, or switch to uploading a file.');
                        return redirect()->to('/admin/settings/hero-banners');
                    }
                } else {
                    $file = $this->request->getFile('image');
                    if (!$file || !$file->isValid()) {
                        $this->session->setFlashdata('error', 'Choose an image file, or switch to using a URL.');
                        return redirect()->to('/admin/settings/hero-banners');
                    }
                    $imageValue = $this->processHeroBannerUpload($file, $uploadPath);
                    if ($imageValue === null) {
                        return redirect()->to('/admin/settings/hero-banners');
                    }
                }

                $maxOrder = (int) ($this->heroBannerModel->where('status', 'active')->selectMax('sort_order')->first()['sort_order'] ?? 0);

                $this->heroBannerModel->insert([
                    'image_filename' => $imageValue,
                    'file_type' => $inputType,
                    'link_url' => $linkUrl,
                    'sort_order' => $maxOrder + 1,
                    'status' => 'active',
                ]);
                $this->session->setFlashdata('success', 'Hero banner slide added.');
            } elseif ($action === 'update') {
                $id = (int) $this->request->getPost('id');
                $slide = $this->heroBannerModel->find($id);
                if (!$slide) {
                    $this->session->setFlashdata('error', 'Slide not found.');
                    return redirect()->to('/admin/settings/hero-banners');
                }

                $linkUrl = trim((string) $this->request->getPost('link_url'));
                if ($linkUrl === '') {
                    $this->session->setFlashdata('error', 'The link cannot be empty.');
                    return redirect()->to('/admin/settings/hero-banners');
                }

                $update = ['link_url' => $linkUrl];
                $inputType = $this->request->getPost('input_type');

                if ($inputType === 'url') {
                    $imageValue = (string) $this->request->getPost('image_url');
                    if ($imageValue !== '') {
                        $update['image_filename'] = $imageValue;
                        $update['file_type'] = 'url';

                        // Switching away from an uploaded file -- it's no
                        // longer referenced by this row, clean it up.
                        if ($slide['file_type'] === 'upload') {
                            $oldPath = $uploadPath . '/' . $slide['image_filename'];
                            if (is_file($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                    }
                    // Empty URL field with "url" selected: no image change,
                    // same as leaving the file input empty under "upload".
                } elseif ($inputType === 'upload') {
                    $file = $this->request->getFile('image');
                    if ($file && $file->isValid() && $file->getSize() > 0) {
                        $filename = $this->processHeroBannerUpload($file, $uploadPath);
                        if ($filename === null) {
                            return redirect()->to('/admin/settings/hero-banners');
                        }
                        $update['image_filename'] = $filename;
                        $update['file_type'] = 'upload';

                        if ($slide['file_type'] === 'upload') {
                            $oldPath = $uploadPath . '/' . $slide['image_filename'];
                            if (is_file($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                    }
                }

                $this->heroBannerModel->update($id, $update);
                $this->session->setFlashdata('success', 'Hero banner slide updated.');
            } elseif ($action === 'remove_to_history') {
                $id = (int) $this->request->getPost('id');
                $this->heroBannerModel->update($id, ['status' => 'history']);
                $this->session->setFlashdata('success', 'Slide moved to history.');
            } elseif ($action === 'restore') {
                $id = (int) $this->request->getPost('id');
                $maxOrder = (int) ($this->heroBannerModel->where('status', 'active')->selectMax('sort_order')->first()['sort_order'] ?? 0);
                $this->heroBannerModel->update($id, ['status' => 'active', 'sort_order' => $maxOrder + 1]);
                $this->session->setFlashdata('success', 'Slide restored to the active carousel.');
            } elseif ($action === 'delete_permanent') {
                $id = (int) $this->request->getPost('id');
                $slide = $this->heroBannerModel->find($id);
                if ($slide && $slide['status'] === 'history') {
                    $path = $uploadPath . '/' . $slide['image_filename'];
                    if (is_file($path)) {
                        unlink($path);
                    }
                    $this->heroBannerModel->delete($id);
                    $this->session->setFlashdata('success', 'Slide permanently deleted.');
                } else {
                    // Only history rows are eligible -- forces the
                    // remove-then-delete two-step the admin UI presents,
                    // rather than letting a crafted request skip straight
                    // from active to gone.
                    $this->session->setFlashdata('error', 'Only slides already in history can be permanently deleted.');
                }
            } elseif ($action === 'reorder') {
                $orderParam = (string) $this->request->getPost('order');
                $ids = array_filter(array_map('intval', explode(',', $orderParam)));

                // Only ids that are actually currently-active slides get
                // touched -- a crafted request can't use this to move a
                // history row's sort_order around, or reference an id that
                // doesn't exist.
                $activeIds = array_column($this->heroBannerModel->where('status', 'active')->select('id')->findAll(), 'id');
                $ids = array_values(array_intersect($ids, $activeIds));

                foreach ($ids as $position => $id) {
                    $this->heroBannerModel->update($id, ['sort_order' => $position + 1]);
                }
                $this->session->setFlashdata('success', 'Slide order updated.');
            }

            return redirect()->to('/admin/settings/hero-banners');
        }

        $user = $this->userModel->find($this->session->get('user_id'));

        return view('admin/settings/hero-banners', [
            'title' => 'Hero Banner - Admin',
            'user' => $user,
            'activeSlides' => $this->heroBannerModel->getActiveSlides(),
            'historySlides' => $this->heroBannerModel->getHistorySlides(),
            'activeTab' => 'hero-banners',
            'minWidth' => self::HERO_BANNER_MIN_WIDTH,
            'minHeight' => self::HERO_BANNER_MIN_HEIGHT,
            'minAspectRatio' => self::HERO_BANNER_MIN_ASPECT_RATIO,
            'maxAspectRatio' => self::HERO_BANNER_MAX_ASPECT_RATIO,
            'maxFileSizeMb' => self::HERO_BANNER_MAX_SIZE / 1024 / 1024,
        ]);
    }

    /**
     * Validates an uploaded hero banner image (type, size, exact pixel
     * dimensions -- the homepage carousel is a fixed size, so anything else
     * would stretch/crop/misalign) and, if it passes, moves it into place
     * under a freshly generated unique name. Returns the new filename, or
     * null after setting a flash error if validation failed.
     */
    private function processHeroBannerUpload($file, string $uploadPath): ?string
    {
        if ($file->getSize() > self::HERO_BANNER_MAX_SIZE) {
            $this->session->setFlashdata('error', 'Image must be under ' . (self::HERO_BANNER_MAX_SIZE / 1024 / 1024) . ' MB.');
            return null;
        }

        if (!in_array($file->getMimeType(), self::HERO_BANNER_ALLOWED_TYPES, true)) {
            $this->session->setFlashdata('error', 'Image must be a JPG, PNG, WebP, or GIF.');
            return null;
        }

        $dimensions = @getimagesize($file->getRealPath());
        if (!$dimensions) {
            $this->session->setFlashdata('error', 'Could not read this image -- the file may be corrupt.');
            return null;
        }
        [$width, $height] = $dimensions;

        if ($width < self::HERO_BANNER_MIN_WIDTH || $height < self::HERO_BANNER_MIN_HEIGHT) {
            $this->session->setFlashdata('error',
                'Image is too low-resolution and would look pixelated -- needs to be at least '
                . self::HERO_BANNER_MIN_WIDTH . '×' . self::HERO_BANNER_MIN_HEIGHT . 'px (got '
                . $width . '×' . $height . 'px).');
            return null;
        }

        $ratio = $width / $height;
        if ($ratio < self::HERO_BANNER_MIN_ASPECT_RATIO || $ratio > self::HERO_BANNER_MAX_ASPECT_RATIO) {
            $this->session->setFlashdata('error',
                'Image shape isn\'t right for a banner -- it looks like a '
                . ($ratio < 1 ? 'mobile screenshot (portrait)' : ($ratio > self::HERO_BANNER_MAX_ASPECT_RATIO ? 'widescreen image' : 'near-square image'))
                . '. Needs a landscape ratio roughly between ' . self::HERO_BANNER_MIN_ASPECT_RATIO . ':1 and '
                . self::HERO_BANNER_MAX_ASPECT_RATIO . ':1 (got ' . round($ratio, 2) . ':1).');
            return null;
        }

        // Timestamp + random suffix, not CI4's getRandomName() -- unique
        // either way, but this keeps upload time visible in the filename
        // for anyone browsing the uploads directory directly.
        $extension = $file->getExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION);
        $filename = 'hero_' . gmdate('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

        $file->move($uploadPath, $filename);

        return $filename;
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
