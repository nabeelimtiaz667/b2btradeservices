<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SiteSettingModel;
use Config\Services;

class SiteSettingsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $settingModel = new SiteSettingModel();
        $siteSettings = $settingModel->getAllSettings();

        $maintenanceMode = $siteSettings['maintenance_mode'] ?? '0';
        $currentPath = $request->getUri()->getPath();

        if ($maintenanceMode === '1') {
            $session = session();
            if ($session->get('user_type') === 'admin') {
                return null;
            }

            $allowedPaths = [
                'login',
                'logout',
                'admin/settings',
                'dashboard/admin',
                'assets/',
                'uploads/',
            ];
            $isAllowed = false;
            $pathLower = ltrim(strtolower($currentPath), '/');
            foreach ($allowedPaths as $path) {
                if ($pathLower === $path || strpos($pathLower, $path) === 0) {
                    $isAllowed = true;
                    break;
                }
            }
            if (!$isAllowed) {
                return Services::response()
                    ->setStatusCode(503)
                    ->setBody(view('errors/maintenance', ['siteSettings' => $siteSettings]));
            }
        }

        $renderer = Services::renderer();
        $renderer->setData(['siteSettings' => $siteSettings], 'raw');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
