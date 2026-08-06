<?php

namespace App\Controllers;

use App\Models\BuyerInquiryModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * XML sitemaps, generated from the live database and cached for a week.
 *
 * Structure -- /sitemap.xml is an index pointing at the child sitemaps:
 *
 *   /sitemap.xml                   index of everything below
 *   /sitemap-categories.xml        supplier category archives   (0.9)
 *   /sitemap-locations.xml         supplier country archives    (0.9)
 *   /sitemap-static.xml            static + listing pages       (0.8)
 *   /sitemap-rfqs-1.xml            buyer inquiries, 50k/file    (0.7)
 *   /sitemap-suppliers-1.xml       supplier profiles, 50k/file  (0.7)
 *
 * "Updated weekly" is handled two ways, because they mean different things:
 *   - <changefreq>weekly</changefreq> tells crawlers how often to come back.
 *   - CACHE_TTL regenerates our own output every 7 days, so inquiries and
 *     suppliers added since the last generation get picked up without anyone
 *     running anything. No cron required, which matters on shared cPanel
 *     hosting where a broken cron would fail silently.
 *
 * Only canonical, indexable URLs are emitted: rows must be published AND have
 * a slug. A slugless supplier would resolve only via its numeric id, which
 * 301s -- and redirects do not belong in a sitemap.
 */
class Sitemap extends BaseController
{
    /** Sitemaps protocol hard limit is 50,000 URLs / 50MB uncompressed. */
    private const MAX_PER_FILE = 50000;

    /** 7 days. Matches the advertised weekly refresh. */
    private const CACHE_TTL = 604800;

    /**
     * Static and listing pages, with their priorities.
     *
     * Auth pages (login/register/forgot-password) are deliberately absent:
     * they are utility, not content, and reset-password is token-scoped so it
     * cannot be crawled meaningfully at all.
     */
    private function staticPages(): array
    {
        return [
            ''                                            => '1.0',
            'about-us'                                    => '0.8',
            'contact'                                     => '0.8',
            'premium-services'                            => '0.8',
            'premium-services/starter-package'            => '0.8',
            'premium-services/gold-package'               => '0.8',
            'premium-services/platinum-package'           => '0.8',
            'premium-services/vip-package'                => '0.8',
            'tradeshow-marketing-services'                => '0.8',
            'become-our-agent-partner'                    => '0.8',
            'success-stories'                             => '0.8',
            'user-guide'                                  => '0.8',
            // Listing/index pages -- entry points to the archives below.
            'buyers'                                      => '0.8',
            'supplier'                                    => '0.8',
            'product'                                     => '0.8',
            'supplier-category'                           => '0.8',
            'supplier-country'                            => '0.8',
            'buyer/post-rfq'                              => '0.8',
            // Policy pages: real content, but low crawl value.
            'privacy-policy'                              => '0.3',
            'refund-policy'                               => '0.3',
            'terms-and-conditions'                        => '0.3',
            'banned-keywords-and-illegal-products-policy' => '0.3',
        ];
    }

    /** /sitemap.xml -- index listing every child sitemap. */
    public function index()
    {
        return $this->render('sitemap_index', function () {
            $maps = [
                'sitemap-categories.xml',
                'sitemap-locations.xml',
                'sitemap-static.xml',
            ];

            foreach (['rfqs' => $this->countInquiries(), 'suppliers' => $this->countSuppliers()] as $name => $total) {
                // Always emit at least one child sitemap, even when a table is
                // empty, so the index never points at nothing and the URL stays
                // stable for anything already submitted to Search Console.
                $pages = max(1, (int) ceil($total / self::MAX_PER_FILE));
                for ($i = 1; $i <= $pages; $i++) {
                    $maps[] = 'sitemap-' . $name . '-' . $i . '.xml';
                }
            }

            $now = date('c');
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                 . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($maps as $map) {
                $xml .= "  <sitemap>\n"
                      . '    <loc>' . $this->esc(base_url($map)) . "</loc>\n"
                      . '    <lastmod>' . $now . "</lastmod>\n"
                      . "  </sitemap>\n";
            }

            return $xml . '</sitemapindex>';
        });
    }

    /** Supplier category archive pages -- /supplier-category/{slug} */
    public function categories()
    {
        return $this->render('sitemap_categories', function () {
            $rows = (new CategoryModel())
                ->select('slug, updated_at, created_at')
                ->where('status', 'active')
                ->where('slug IS NOT NULL', null, false)
                ->where('slug !=', '')
                ->orderBy('id', 'ASC')
                ->findAll();

            $urls = [];
            foreach ($rows as $r) {
                $urls[] = [
                    'loc'     => base_url('supplier-category/' . $r['slug']),
                    'lastmod' => $this->lastmod($r),
                ];
            }

            return $this->urlset($urls, '0.9');
        });
    }

    /** Country archive pages -- /supplier-country/{code} */
    public function locations()
    {
        return $this->render('sitemap_locations', function () {
            $rows = (new CountryModel())
                ->select('code, updated_at, created_at')
                ->where('status', 'active')
                ->where('code IS NOT NULL', null, false)
                ->where('code !=', '')
                ->orderBy('name', 'ASC')
                ->findAll();

            $urls = [];
            foreach ($rows as $r) {
                $urls[] = [
                    'loc'     => base_url('supplier-country/' . $r['code']),
                    'lastmod' => $this->lastmod($r),
                ];
            }

            return $this->urlset($urls, '0.9');
        });
    }

    /** Static + listing pages. */
    public function staticPagesMap()
    {
        return $this->render('sitemap_static', function () {
            $now  = date('c');
            $urls = [];

            foreach ($this->staticPages() as $path => $priority) {
                $urls[] = [
                    'loc'      => base_url($path),
                    'lastmod'  => $now,
                    'priority' => $priority,
                ];
            }

            return $this->urlset($urls, '0.8');
        });
    }

    /** Buyer inquiries -- /buyer-inquiry/{slug}, chunked at 50k. */
    public function inquiries(int $page = 1)
    {
        $this->guardPage($page, $this->countInquiries());

        return $this->render('sitemap_rfqs_' . $page, function () use ($page) {
            $rows = (new BuyerInquiryModel())
                ->select('slug, updated_at, created_at')
                ->where('status', 'active')
                ->where('slug IS NOT NULL', null, false)
                ->where('slug !=', '')
                ->orderBy('id', 'ASC')
                ->findAll(self::MAX_PER_FILE, ($page - 1) * self::MAX_PER_FILE);

            $urls = [];
            foreach ($rows as $r) {
                $urls[] = [
                    'loc'     => base_url('buyer-inquiry/' . $r['slug']),
                    'lastmod' => $this->lastmod($r),
                ];
            }

            return $this->urlset($urls, '0.7');
        });
    }

    /** Supplier profiles -- /supplier/profile/{slug}, chunked at 50k. */
    public function suppliers(int $page = 1)
    {
        $this->guardPage($page, $this->countSuppliers());

        return $this->render('sitemap_suppliers_' . $page, function () use ($page) {
            $rows = (new UserModel())
                ->select('slug, updated_at, created_at')
                ->where('user_type', 'supplier')
                ->where('status', 'approved')
                ->where('slug IS NOT NULL', null, false)
                ->where('slug !=', '')
                ->orderBy('id', 'ASC')
                ->findAll(self::MAX_PER_FILE, ($page - 1) * self::MAX_PER_FILE);

            $urls = [];
            foreach ($rows as $r) {
                $urls[] = [
                    'loc'     => base_url('supplier/profile/' . $r['slug']),
                    'lastmod' => $this->lastmod($r),
                ];
            }

            return $this->urlset($urls, '0.7');
        });
    }

    // ---------------------------------------------------------------- helpers

    private function countInquiries(): int
    {
        return (new BuyerInquiryModel())
            ->where('status', 'active')
            ->where('slug IS NOT NULL', null, false)
            ->where('slug !=', '')
            ->countAllResults();
    }

    private function countSuppliers(): int
    {
        return (new UserModel())
            ->where('user_type', 'supplier')
            ->where('status', 'approved')
            ->where('slug IS NOT NULL', null, false)
            ->where('slug !=', '')
            ->countAllResults();
    }

    /**
     * 404 for a page number the data does not support, so crawlers are not
     * handed empty sitemaps for pages that will never exist. Page 1 is always
     * allowed -- it is what the index points at when a table is empty.
     */
    private function guardPage(int $page, int $total): void
    {
        $pages = max(1, (int) ceil($total / self::MAX_PER_FILE));

        if ($page < 1 || $page > $pages) {
            throw new PageNotFoundException('Sitemap page not found');
        }
    }

    private function lastmod(array $row): string
    {
        $ts = $row['updated_at'] ?? null;

        if (empty($ts)) {
            $ts = $row['created_at'] ?? null;
        }

        // strtotime(null) is deprecated on PHP 8.1 and returns the epoch, which
        // would publish a 1970 lastmod. Fall back to now instead.
        if (empty($ts)) {
            return date('c');
        }

        $parsed = strtotime((string) $ts);

        return $parsed ? date('c', $parsed) : date('c');
    }

    /** Build a <urlset> from [loc, lastmod, priority?] rows. */
    private function urlset(array $urls, string $defaultPriority): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n"
                  . '    <loc>' . $this->esc($u['loc']) . "</loc>\n"
                  . '    <lastmod>' . $u['lastmod'] . "</lastmod>\n"
                  . "    <changefreq>weekly</changefreq>\n"
                  . '    <priority>' . ($u['priority'] ?? $defaultPriority) . "</priority>\n"
                  . "  </url>\n";
        }

        return $xml . '</urlset>';
    }

    /**
     * Slugs are [a-z0-9-] and country codes are alphanumeric, so nothing here
     * should need escaping -- but a URL is still text going into XML, and
     * &, < and > would make the document unparseable if one ever slipped in.
     */
    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Cache the generated XML for a week and return it with the right headers.
     *
     * The cache is what makes this "weekly": output is rebuilt from the live
     * database once the entry expires, so new rows appear without any manual
     * step. Clear it early with `php spark cache:clear` after a bulk import.
     */
    private function render(string $cacheKey, callable $build): ResponseInterface
    {
        $cache = \Config\Services::cache();
        $xml   = $cache->get($cacheKey);

        if ($xml === null) {
            $xml = $build();
            $cache->save($cacheKey, $xml, self::CACHE_TTL);
        }

        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->setBody($xml);
    }
}
