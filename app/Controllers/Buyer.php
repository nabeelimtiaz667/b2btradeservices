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
            'metaDescription' => 'Browse the latest buyer inquiries and RFQs on B2B Trade Services. Find genuine buying leads and connect with importers looking for your products.',
            'canonical' => canonical_self_url(),
            'inquiries' => $inquiries,
            'pager' => $pager,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'featuredSuppliers' => $this->getFeaturedSuppliers(),
        ];

        return view('pages/buyer-main', $data);
    }

    /**
     * Single funnel for every historic inquiry URL shape, all of which are
     * id-based and all of which 301 onto the canonical slug URL:
     *
     *   /buyer/detail/{id}
     *   /buyer-detail/{id}
     *   /buyer-inquiry/{anything}/{id}   <- the old unvalidated-slug form
     */
    public function legacyRedirect($id = null)
    {
        if (!$id || !ctype_digit((string) $id)) {
            return redirect()->to(base_url('buyers'), 301);
        }

        $inquiry = $this->inquiryModel->find((int) $id);

        if (!$inquiry || $inquiry['status'] !== 'active') {
            // A hard 404 rather than a soft one (a 301 to the listing), so search
            // engines drop the dead URL instead of treating /buyers as a duplicate
            // of every removed inquiry.
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inquiry not found');
        }

        if (empty($inquiry['slug'])) {
            // Backfill has not run yet. Render rather than redirect to a URL that
            // cannot resolve. This is what makes deploy order safe in either
            // direction, and it cannot loop: detail() in this state falls through
            // to render instead of redirecting back.
            return $this->detail((string) $id);
        }

        return redirect()->to(base_url('buyer-inquiry/' . $inquiry['slug']), 301);
    }

    public function detail($slugOrId = null)
    {
        if ($slugOrId === null || $slugOrId === '') {
            return redirect()->to(base_url('buyers'), 301);
        }

        $slugOrId = (string) $slugOrId;

        // Slug lookup first, numeric fallback second. This is deliberately the
        // reverse of Supplier::profile(), which tests is_numeric() first: a title
        // like "2024 Steel Tender" slugifies to an all-numeric slug, and that row
        // must win over a same-numbered id.
        $inquiry = $this->inquiryModel->getInquiryBySlug($slugOrId);

        if ($inquiry === null && ctype_digit($slugOrId)) {
            $inquiry = $this->inquiryModel->find((int) $slugOrId);

            if ($inquiry !== null && $inquiry['status'] === 'active' && !empty($inquiry['slug'])) {
                return redirect()->to(base_url('buyer-inquiry/' . $inquiry['slug']), 301);
            }
        }

        if (!$inquiry || $inquiry['status'] !== 'active') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inquiry not found');
        }

        // find(null) returns every row rather than null, so an inquiry with no
        // category or country would hand the view a 122-element list and the
        // view's ['name'] lookup would fatal. The public RFQ form can submit
        // without a country, so this is reachable.
        $inquiry['category'] = !empty($inquiry['category_id'])
            ? $this->categoryModel->find($inquiry['category_id'])
            : null;
        $inquiry['country'] = !empty($inquiry['country_id'])
            ? $this->countryModel->find($inquiry['country_id'])
            : null;

        $relatedInquiries = !empty($inquiry['category_id'])
            ? $this->inquiryModel->getInquiriesByCategory($inquiry['category_id'], 5, $inquiry['id'])
            : [];

        foreach ($relatedInquiries as &$related) {
            $related['country'] = !empty($related['country_id'])
                ? $this->countryModel->find($related['country_id'])
                : null;
        }
        unset($related);

        $data = [
            'title' => $inquiry['title'],
            'canonical' => inquiry_url($inquiry),
            'metaDescription' => inquiry_meta_description($inquiry),
            'canViewPremiumDetails' => $this->canViewPremiumDetails(),
            'inquiry' => $inquiry,
            'relatedInquiries' => $relatedInquiries,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
        ];

        return view('pages/buyer-detail', $data);
    }

    /**
     * Whether the current visitor may see an inquiry's buyer contact details
     * (name, phone, company) instead of the "Premium Members only" mask.
     *
     * Admins always can. Otherwise this requires a paid membership tier on
     * the *logged-in* user's own account, checked live against the database
     * rather than trusted from session data — membership_level is never
     * written into the session (Auth.php only stores user_id/name/email/
     * user_type/status), and re-querying means an admin changing someone's
     * tier takes effect on their very next page load, not after a re-login.
     */
    private function canViewPremiumDetails(): bool
    {
        if (session()->get('user_type') === 'admin') {
            return true;
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return false;
        }

        $user = $this->userModel->find($userId);
        $premiumTiers = ['starter', 'gold', 'platinum', 'vip'];

        return $user && in_array($user['membership_level'] ?? 'free', $premiumTiers, true);
    }

    public function postRfq()
    {
        $data = [
            'title' => 'Post a Buy Offer',
            'metaDescription' => 'Post your buying requirement or RFQ on B2B Trade Services and let verified suppliers reach out to you directly with quotes.',
            'canonical' => current_url(),
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
            'title' => $keyword ? 'Search Results for "' . $keyword . '" - Buyer Inquiries' : 'Search Buyer Inquiries',
            'metaDescription' => $keyword
                ? 'Buyer inquiries matching "' . $keyword . '" on B2B Trade Services.'
                : 'Search buyer inquiries and RFQs on B2B Trade Services by keyword, category, or country.',
            'canonical' => canonical_self_url(),
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
            
            // Not esc()'d here: $categoryName only ever flows into 'title' and
            // 'metaDescription', both of which the layout already escapes
            // exactly once. Escaping it here too would double-encode any
            // category name containing '&' (e.g. "Building & Construction"
            // rendering as "Building &amp;amp; Construction").
            $categoryName = $category['name'];
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
            'metaDescription' => 'Browse ' . $categoryName . ' buyer inquiries and RFQs on B2B Trade Services and connect with buyers looking for your products.',
            'canonical' => current_url(),
            'inquiries' => $inquiries,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'featuredSuppliers' => $this->getFeaturedSuppliers(),
            'currentCategory' => $categoryName,
        ];

        return view('pages/buyer-main', $data);
    }
}
