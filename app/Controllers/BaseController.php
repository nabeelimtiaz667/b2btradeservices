<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Atomically increments a popularity-tracking column (products.view_count,
     * users.profile_view_count) on a real page view, deduped per visitor
     * session so refreshing the same page repeatedly doesn't inflate the
     * count. Used by Product::detail() and Supplier::profile() to feed
     * Pages.php's Top Products/Top Suppliers ranking -- see CHANGELOG
     * 2026-08-21.
     *
     * Deliberately a raw query-builder increment (SET col = col + 1), not
     * Model::update(): atomic (no read-modify-write race between concurrent
     * viewers) and bypasses Model validation entirely, which matters here --
     * UserModel's email uniqueness rule has no business running on a view-
     * count bump (see BLOCKERS #22 for why that rule is fragile to begin with).
     */
    protected function trackView(string $table, int $id, string $column, string $sessionKey): void
    {
        $viewed = session()->get($sessionKey) ?? [];

        if (in_array($id, $viewed, true)) {
            return;
        }

        \Config\Database::connect()
            ->table($table)
            ->where('id', $id)
            ->set($column, $column . ' + 1', false)
            ->update();

        $viewed[] = $id;
        session()->set($sessionKey, $viewed);
    }
}
