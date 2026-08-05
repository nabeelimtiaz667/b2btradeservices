<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CountryModel;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;

class Pages extends BaseController
{
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
