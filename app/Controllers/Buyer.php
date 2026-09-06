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
            'resultsTotal' => $pager->getTotal('buyer'),
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'featuredSuppliers' => $this->getFeaturedSuppliers(),
        ];

        $tier = $this->contentAccessTier();
        $data['gateTier'] = ($pager->getCurrentPage('buyer') > 1 && $tier !== 'privileged') ? $tier : null;

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
     * Just the 'privileged' case of BaseController::contentAccessTier() --
     * see that method for the admin/tier check itself.
     */
    private function canViewPremiumDetails(): bool
    {
        return $this->contentAccessTier() === 'privileged';
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

    /**
     * Clean-URL search: /buyer/search/{keyword}/country/{code}/date/{date}.
     *
     * The old /buyer/search?q=...&country=...&date=... form still works --
     * a submitted GET form or an old link can only produce a query string --
     * but it 301s to the equivalent clean path rather than rendering
     * directly, so the query-string form is never the canonical URL search
     * engines see. See parse_search_path()/build_search_path() in
     * seo_helper.php for the shared segment encode/decode.
     */
    public function search(...$segments)
    {
        // CI4 re-splits an (:any) route capture back into one method argument
        // per '/'-delimited piece rather than handing the whole string to a
        // single parameter -- variadic capture + rejoin recovers it reliably
        // regardless of how many segments the URL has.
        $pathParams = $segments === [] ? null : implode('/', $segments);

        $knownKeys = ['category', 'country', 'date', 'page'];

        if ($pathParams === null && service('request')->getUri()->getQuery() !== '') {
            $filters = [];

            if ($categoryId = $this->request->getGet('category')) {
                $cat = $this->categoryModel->find($categoryId);
                if (! empty($cat['slug'])) {
                    $filters['category'] = $cat['slug'];
                }
            }

            if ($countryId = $this->request->getGet('country')) {
                $country = $this->countryModel->find($countryId);
                if (! empty($country['code'])) {
                    $filters['country'] = $country['code'];
                }
            }

            if ($date = $this->request->getGet('date')) {
                $filters['date'] = $date;
            }

            $clean = build_search_path($this->request->getGet('q'), $filters);

            return redirect()->to(base_url('buyer/search' . ($clean !== '' ? '/' . $clean : '')), 301);
        }

        $parsed  = parse_search_path($pathParams, $knownKeys);
        $keyword = $parsed['keyword'];
        $filters = $parsed['filters'];

        $categoryId = null;
        if (! empty($filters['category'])) {
            $cat = $this->categoryModel->getCategoryBySlug($filters['category']);
            $categoryId = $cat['id'] ?? null;
        }

        $countryId = null;
        if (! empty($filters['country'])) {
            $country = $this->countryModel->getCountryByCode($filters['country']);
            $countryId = $country['id'] ?? null;
        }

        $date = $filters['date'] ?? null;

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

        $perPage = 50;
        $total = $builder->countAllResults(false);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $rawPage = $filters['page'] ?? null;
        $page = min(max(1, (int) ($rawPage ?? 1)), $totalPages);

        // See the matching check in Product::search() -- redirects an
        // invalid or out-of-range page/... segment to the URL for the actual
        // page being rendered instead of silently rendering it under the
        // wrong one.
        if ($rawPage !== null && $rawPage !== (string) $page) {
            $canonicalFilters = [
                'category' => $filters['category'] ?? null,
                'country' => $filters['country'] ?? null,
                'date' => $filters['date'] ?? null,
            ];
            $canonicalFilters['page'] = $page > 1 ? (string) $page : null;
            $clean = build_search_path($keyword, $canonicalFilters);

            return redirect()->to(base_url('buyer/search' . ($clean !== '' ? '/' . $clean : '')), 301);
        }

        $inquiries = $builder
            ->orderBy('inquiry_date', 'DESC')
            ->findAll($perPage, ($page - 1) * $perPage);

        foreach ($inquiries as &$inquiry) {
            $inquiry['category'] = $this->categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $this->countryModel->find($inquiry['country_id']);
        }

        $searchPager = build_search_pager('buyer/search', $keyword, [
            'category' => $filters['category'] ?? null,
            'country' => $filters['country'] ?? null,
            'date' => $filters['date'] ?? null,
        ], $page, $totalPages);

        $data = [
            'title' => $keyword ? 'Search Results for "' . $keyword . '" - Buyer Inquiries' : 'Search Buyer Inquiries',
            'metaDescription' => $keyword
                ? 'Buyer inquiries matching "' . $keyword . '" on B2B Trade Services.'
                : 'Search buyer inquiries and RFQs on B2B Trade Services by keyword, category, or country.',
            'canonical' => current_url(),
            'inquiries' => $inquiries,
            'searchPager' => $searchPager,
            'resultsTotal' => $total,
            'categories' => $this->categoryModel->getActiveCategories(),
            'countries' => $this->countryModel->getActiveCountries(),
            'featuredSuppliers' => $this->getFeaturedSuppliers(),
            'searchKeyword' => $keyword,
        ];

        $tier = $this->contentAccessTier();
        $data['gateTier'] = ($page > 1 && $tier !== 'privileged') ? $tier : null;

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
