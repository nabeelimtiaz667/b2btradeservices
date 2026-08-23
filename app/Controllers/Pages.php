<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CountryModel;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;
use App\Models\SiteSettingModel;
use App\Models\HeroBannerSlideModel;

class Pages extends BaseController
{
    /**
     * How many slots index.php's Top Products / Top Suppliers sections
     * actually render (array_slice($x, 0, N) in the view).
     */
    private const TOP_PRODUCTS_DISPLAY_COUNT = 3;
    private const TOP_SUPPLIERS_DISPLAY_COUNT = 2;

    /**
     * Bounds for the admin-configurable set count / rotation interval (see
     * AdminSettings::topSections()) -- re-clamped here too in case a stray
     * DB value (or a row edited directly) falls outside what that form
     * allows.
     */
    private const TOP_SET_COUNT_MIN = 1;
    private const TOP_SET_COUNT_MAX = 10;
    private const TOP_INTERVAL_SECONDS_MIN = 2;
    private const TOP_INTERVAL_SECONDS_MAX = 60;

    /**
     * Latest Buy Offers carousel: no admin panel for this one (unlike Top
     * Products/Suppliers) -- just rotate through 3 slides covering the most
     * recent inquiries. PER_SET is the original single-list size (unchanged
     * from before this was a carousel at all) -- only the number of
     * rotating slides is new, not how many offers show at once. POOL_SIZE
     * is PER_SET x 3 slides; if fewer offers exist than that, the pool --
     * and the carousel -- is just however many actually exist.
     */
    private const LATEST_BUY_OFFERS_PER_SET = 8;
    private const LATEST_BUY_OFFERS_SET_COUNT = 3;
    private const LATEST_BUY_OFFERS_POOL_SIZE = self::LATEST_BUY_OFFERS_PER_SET * self::LATEST_BUY_OFFERS_SET_COUNT;
    private const LATEST_BUY_OFFERS_INTERVAL_SECONDS = 5;

    /**
     * Per-page title and meta description for every static page this
     * controller serves. Replaces the old ucfirst(str_replace('-', ' ', $page))
     * title, which only capitalized the first letter of the whole slug (e.g.
     * "banned-keywords-and-illegal-products-policy" -> "Banned keywords and
     * illegal products policy") and never set a description at all -- every
     * one of these pages fell through to the single site-wide
     * meta_description, so they were all identical in search results.
     *
     * The homepage ('index') is deliberately absent: it keeps using
     * $siteSettings['meta_title']/['meta_description'] as its title/description,
     * since those settings exist precisely to be the site's own identity, and
     * the homepage is the one page where that's the correct value rather than
     * a fallback being reused where it shouldn't be.
     */
    private function getPageMeta(): array
    {
        return [
            'about-us' => [
                'title' => 'About Us',
                'description' => 'B2B Trade Services connects suppliers, buyers, and entrepreneurs in one trusted marketplace, with company formation and trade consultancy services.',
            ],
            'contact' => [
                'title' => 'Contact Us',
                'description' => 'Get in touch with B2B Trade Services. Find our office locations or send us a message for support with your buying and selling needs.',
            ],
            'success-stories' => [
                'title' => 'Success Stories',
                'description' => 'Real success stories from buyers and suppliers who found reliable trade partners through B2B Trade Services.',
            ],
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'description' => 'How B2B Trade Services collects, uses, shares, and protects your information when you use our website and services.',
            ],
            'refund-policy' => [
                'title' => 'Refund Policy',
                'description' => 'The conditions under which refunds may be requested and processed for B2B Trade Services memberships and services.',
            ],
            'terms-and-conditions' => [
                'title' => 'Terms and Conditions',
                'description' => 'The Terms and Conditions governing your access to and use of the B2B Trade Services website and marketplace.',
            ],
            'user-guide' => [
                'title' => 'User Guide',
                'description' => 'A quick reference guide to using the B2B Trade Services platform to find partners, post inquiries, and grow your business.',
            ],
            'banned-keywords-and-illegal-products-policy' => [
                'title' => 'Banned Keywords and Illegal Products Policy',
                'description' => 'Our policy on banned keywords and prohibited products, in place to ensure legal compliance, platform safety, and responsible trade.',
            ],
            'become-our-agent-partner' => [
                'title' => 'Become Our Agent Partner',
                'description' => 'Join the B2B Trade Services Agency Partnership Program and earn commissions by referring businesses to our global B2B marketplace.',
            ],
            'tradeshow-marketing-services' => [
                'title' => 'Trade Show Marketing Services',
                'description' => 'Trade show and exhibition marketing services from B2B Trade Services to help you drive traffic, generate leads, and boost your brand presence.',
            ],
            'premium-services' => [
                'title' => 'Premium Membership Plans',
                'description' => 'Compare Starter, Gold, Platinum, and VIP membership plans on B2B Trade Services and get up to 10x more leads with premium features.',
            ],
            'starter-package' => [
                'title' => 'Starter Package',
                'description' => 'The Starter membership plan on B2B Trade Services: an official company profile page and a 10-product online store, from $499/year.',
            ],
            'gold-package' => [
                'title' => 'Gold Package',
                'description' => 'The Gold membership plan on B2B Trade Services: a 20-product store, buyer database access, and a dedicated consultant, from $1,499/year.',
            ],
            'platinum-package' => [
                'title' => 'Platinum Package',
                'description' => 'The Platinum membership plan on B2B Trade Services: a 30-product store, a consultant team, and LLC/LTD company registration, from $3,999/year.',
            ],
            'vip-package' => [
                'title' => 'VIP Package',
                'description' => 'The VIP membership plan on B2B Trade Services: a 50-product store, Google SEO, and full buyer database access, from $7,999/year.',
            ],
        ];
    }

    public function index($page = 'index')
    {
        if (! is_file(APPPATH . 'Views/pages/' . $page . '.php')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }

        $meta = $this->getPageMeta()[$page] ?? null;

        $data['title'] = $meta['title'] ?? null;
        $data['metaDescription'] = $meta['description'] ?? null;
        $data['canonical'] = current_url();

        if ($page === 'index') {
            $categoryModel = new CategoryModel();

            $countryModel = new CountryModel();
            $userModel = new UserModel();
            $productModel = new ProductModel();
            $inquiryModel = new BuyerInquiryModel();
            $settingModel = new SiteSettingModel();
            $heroBannerModel = new HeroBannerSlideModel();

            $data['categories'] = $categoryModel->getActiveCategories();
            $data['countries'] = $countryModel->getActiveCountries();
            $data['heroBannerSlides'] = $heroBannerModel->getActiveSlides();

            $topSuppliersSetCount = max(self::TOP_SET_COUNT_MIN, min(self::TOP_SET_COUNT_MAX,
                (int) $settingModel->getSetting('top_suppliers_set_count', 1)));
            $topSuppliersIntervalSeconds = max(self::TOP_INTERVAL_SECONDS_MIN, min(self::TOP_INTERVAL_SECONDS_MAX,
                (int) $settingModel->getSetting('top_suppliers_interval_seconds', 5)));

            // "Top Suppliers" carousel: one rotating set per
            // topSuppliersSetCount (admin-configurable, see
            // AdminSettings::topSections()). Every slot in every set is
            // filled by a "hotness" score: profile views decayed by age
            // (views / (days-old + 2)), the same recency-decay ranking
            // Hacker News/Reddit use, so a supplier added yesterday with a
            // handful of views can already outrank one from a year ago with
            // the same raw view count spread thin over time. Membership tier
            // is a modest *multiplier* on that score (paid tiers keep a
            // visibility edge, consistent with how membership is weighted
            // elsewhere on the site) rather than a hard gate.
            //
            // No manual pinning here (removed 2026-08-23 -- is_featured is
            // used elsewhere on the homepage, see Featured Suppliers/
            // Products further down, and mixing that flag into this fully-
            // automatic ranking too was confusing). A supplier already used
            // in an earlier set is excluded from every later set's fill, so
            // the same supplier never appears twice across the whole
            // carousel.
            $membershipWeight = "CASE membership_level "
                . "WHEN 'platinum' THEN 1.5 "
                . "WHEN 'gold' THEN 1.3 "
                . "WHEN 'silver' THEN 1.15 "
                . "WHEN 'starter' THEN 1.05 "
                . "ELSE 1.0 END";
            $supplierHotness = "((profile_view_count / (DATEDIFF(UTC_DATE(), created_at) + 2)) * ($membershipWeight))";

            $supplierSets = [];
            $usedSupplierIds = [];

            for ($setNumber = 1; $setNumber <= $topSuppliersSetCount; $setNumber++) {
                $dynamicBuilder = $userModel
                    ->select("users.*, $supplierHotness AS hotness_score")
                    ->where('user_type', 'supplier')
                    ->where('status', 'approved')
                    ->orderBy('hotness_score', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->limit(self::TOP_SUPPLIERS_DISPLAY_COUNT + 6);

                if (!empty($usedSupplierIds)) {
                    $dynamicBuilder->whereNotIn('id', $usedSupplierIds);
                }

                $dynamicSuppliers = $dynamicBuilder->findAll();

                // Same reasoning as the pre-carousel version this replaced:
                // a plain shuffle() of the whole pool gives the genuine
                // top-ranked item the same odds as the barely-qualifying
                // last one, which defeats ranking by real popularity.
                // Anchoring the #1 slot fixes that.
                $dynamicSuppliers = $this->anchorTopAndShuffleRest($dynamicSuppliers);

                $setSuppliers = array_slice($dynamicSuppliers, 0, self::TOP_SUPPLIERS_DISPLAY_COUNT);

                foreach ($setSuppliers as &$s) {
                    $s['country'] = $countryModel->find($s['country_id']);
                    $s['products'] = $productModel
                        ->where('supplier_id', $s['id'])
                        ->where('status', 'active')
                        ->limit(3)
                        ->findAll();
                }
                unset($s);

                $supplierSets[] = $setSuppliers;
                $usedSupplierIds = array_merge($usedSupplierIds, array_column($setSuppliers, 'id'));
            }

            $data['topSupplierSets'] = $supplierSets;
            $data['topSuppliersIntervalSeconds'] = $topSuppliersIntervalSeconds;

            // "Featured Suppliers" (the further-down, category-grouped
            // homepage section -- separate from the Top Suppliers carousel
            // above). Default: premium (platinum/gold) members only, same
            // as always, shuffled for rotation. If the admin has turned on
            // "Show Starred Supplier as Featured" (Manage Suppliers page),
            // starred suppliers go *first* -- guaranteed to show, not just
            // added to the same shuffle where they might not appear on a
            // given load -- and premium members (shuffled among themselves)
            // fill whatever capacity is left, exactly as before. Turning
            // the setting on can only add/prioritize suppliers, never
            // remove the existing premium ones.
            $showStarredAsFeatured = $settingModel->getSetting('show_starred_suppliers_as_featured', '0') === '1';

            $premiumSuppliers = $userModel
                ->where('user_type', 'supplier')
                ->where('status', 'approved')
                ->whereIn('membership_level', ['platinum', 'gold'])
                ->findAll();

            $starredSuppliers = [];
            if ($showStarredAsFeatured) {
                $allStarred = $userModel
                    ->where('user_type', 'supplier')
                    ->where('status', 'approved')
                    ->where('is_featured', 1)
                    ->findAll();

                // Dedup: a supplier who's both starred and platinum/gold
                // counts as "starred" for ordering purposes (guaranteed
                // first), not duplicated into the premium list too.
                $starredIds = array_column($allStarred, 'id');
                $premiumSuppliers = array_values(array_filter(
                    $premiumSuppliers,
                    fn($s) => !in_array($s['id'], $starredIds, true)
                ));

                $starredSuppliers = $allStarred;
                shuffle($starredSuppliers);
            }

            shuffle($premiumSuppliers);

            $categorySuppliers = array_merge($starredSuppliers, $premiumSuppliers);

            foreach ($categorySuppliers as &$cs) {
                $cs['country'] = $countryModel->find($cs['country_id']);
                $cs['products'] = $productModel
                    ->where('supplier_id', $cs['id'])
                    ->where('status', 'active')
                    ->limit(3)
                    ->findAll();
            }
            unset($cs);

            $data['categorySuppliers'] = $categorySuppliers;

            $topProductsSetCount = max(self::TOP_SET_COUNT_MIN, min(self::TOP_SET_COUNT_MAX,
                (int) $settingModel->getSetting('top_products_set_count', 1)));
            $topProductsIntervalSeconds = max(self::TOP_INTERVAL_SECONDS_MIN, min(self::TOP_INTERVAL_SECONDS_MAX,
                (int) $settingModel->getSetting('top_products_interval_seconds', 5)));

            // "Top Products" carousel: same rotating-sets approach as Top
            // Suppliers above -- one set per topProductsSetCount, every slot
            // filled by the hotness ranking (view_count decayed by age). No
            // manual pinning (removed 2026-08-23 -- same reasoning as Top
            // Suppliers above: is_featured already drives the separate
            // Featured Products section further down the homepage, and
            // having it also pin slots here was confusing given the two
            // sections have very different capacity needs).
            $productHotness = "(view_count / (DATEDIFF(UTC_DATE(), created_at) + 2))";

            $productSets = [];
            $usedProductIds = [];

            for ($setNumber = 1; $setNumber <= $topProductsSetCount; $setNumber++) {
                $dynamicBuilder = $productModel
                    ->select("products.*, $productHotness AS hotness_score")
                    ->where('status', 'active')
                    ->orderBy('hotness_score', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->limit(self::TOP_PRODUCTS_DISPLAY_COUNT + 5);

                if (!empty($usedProductIds)) {
                    $dynamicBuilder->whereNotIn('id', $usedProductIds);
                }

                $dynamicProducts = $dynamicBuilder->findAll();

                // Same reasoning as Top Suppliers: anchor the genuine #1
                // slot so it's always shown, only rotate the rest.
                $dynamicProducts = $this->anchorTopAndShuffleRest($dynamicProducts);

                $setProducts = array_slice($dynamicProducts, 0, self::TOP_PRODUCTS_DISPLAY_COUNT);

                foreach ($setProducts as &$p) {
                    $supplier = $userModel->find($p['supplier_id']);
                    if ($supplier) {
                        $supplier['country'] = $countryModel->find($supplier['country_id']);
                    }
                    $p['supplier'] = $supplier;
                    $p['category'] = $categoryModel->find($p['category_id']);
                }
                unset($p);

                $productSets[] = $setProducts;
                $usedProductIds = array_merge($usedProductIds, array_column($setProducts, 'id'));
            }

            $data['topProductSets'] = $productSets;
            $data['topProductsIntervalSeconds'] = $topProductsIntervalSeconds;

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

            // Rotating carousel, no admin config for this one -- just the
            // most recent inquiries, chunked into fixed-size slides in
            // recency order (not shuffled: unlike Top Products/Suppliers
            // there's no ranking ambiguity to break ties on, "latest" is
            // already a total order).
            $latestInquiries = $inquiryModel->orderBy('inquiry_date', 'DESC')->getActiveInquiries(self::LATEST_BUY_OFFERS_POOL_SIZE);

            foreach ($latestInquiries as &$inq) {
                if (!empty($inq['country_id'])) {
                    $country = $countryModel->find($inq['country_id']);
                    if ($country) {
                        $inq['country_flag'] = $country['flag'] ?? '';
                        $inq['country_name'] = $country['name'] ?? '';
                    }
                }
            }
            unset($inq);

            $data['latestBuyOfferSets'] = array_chunk($latestInquiries, self::LATEST_BUY_OFFERS_PER_SET);
            $data['latestBuyOffersIntervalSeconds'] = self::LATEST_BUY_OFFERS_INTERVAL_SECONDS;

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

    /**
     * Keeps a ranked pool's #1 item always first, then shuffles the rest of
     * the pool behind it -- for the homepage's Top Products/Top Suppliers
     * sections, where the caller then does array_slice($pool, 0, N) to get
     * the N displayed. A plain shuffle() of the whole pool was tried first
     * and measured broken: it gives the genuine top-ranked item the same
     * odds of being displayed as the pool's weakest qualifier, which defeats
     * ranking by real popularity (view_count decayed by age) at all. This
     * keeps rank #1 guaranteed while still rotating who fills the rest of
     * the visible slots, for some discovery variety per page load.
     */
    private function anchorTopAndShuffleRest(array $rankedPool): array
    {
        if (count($rankedPool) <= 1) {
            return $rankedPool;
        }

        $top = array_shift($rankedPool);
        shuffle($rankedPool);
        array_unshift($rankedPool, $top);

        return $rankedPool;
    }
}
