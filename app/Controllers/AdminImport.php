<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\CountryModel;
use App\Models\ProductModel;
use App\Models\BuyerInquiryModel;

class AdminImport extends BaseController
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

    private function checkAdmin()
    {
        if (!$this->session->get('logged_in') || $this->session->get('user_type') !== 'admin') {
            return false;
        }
        return true;
    }

    private function buildLookupMaps()
    {
        $countries = $this->countryModel->findAll();
        $countryMap = [];
        foreach ($countries as $c) {
            $countryMap[strtolower(trim($c['name']))] = $c['id'];
            if (!empty($c['code'])) {
                $countryMap[strtolower(trim($c['code']))] = $c['id'];
            }
        }

        $categories = $this->categoryModel->findAll();
        $categoryMap = [];
        foreach ($categories as $c) {
            $categoryMap[strtolower(trim($c['name']))] = $c['id'];
            if (!empty($c['slug'])) {
                $categoryMap[strtolower(trim($c['slug']))] = $c['id'];
            }
        }

        return [$countryMap, $categoryMap];
    }

    private function parseCsv($file)
    {
        $rows = [];
        $malformed = [];
        if (($handle = fopen($file->getTempName(), 'r')) !== false) {
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                return [[], [], []];
            }
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace(["\xEF\xBB\xBF"], '', $h)));
            }, $headers);

            $lineNum = 1;
            while (($data = fgetcsv($handle)) !== false) {
                $lineNum++;
                if (count($data) === count($headers)) {
                    $rows[] = array_combine($headers, $data);
                } else {
                    $malformed[] = ['row' => $lineNum, 'status' => 'Skipped', 'reason' => 'Column count mismatch (expected ' . count($headers) . ', got ' . count($data) . ')'];
                }
            }
            fclose($handle);
        }
        return [$headers ?? [], $rows, $malformed];
    }

    public function suppliers()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $data = [
            'title' => 'Import Data',
            'user' => $user,
            'activeTab' => 'suppliers',
            'results' => null,
        ];

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('csv_file');
            if (!$file || !$file->isValid() || $file->getExtension() !== 'csv') {
                $this->session->setFlashdata('error', 'Please upload a valid CSV file.');
                return redirect()->to('/admin/import/suppliers');
            }

            [$headers, $rows, $malformed] = $this->parseCsv($file);
            if (empty($rows) && empty($malformed)) {
                $this->session->setFlashdata('error', 'CSV file is empty or has invalid format.');
                return redirect()->to('/admin/import/suppliers');
            }

            [$countryMap, $categoryMap] = $this->buildLookupMaps();
            $results = $malformed;
            $imported = 0;
            $skipped = count($malformed);

            $this->userModel->skipValidation(true);

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2;
                $name = trim($row['name'] ?? '');
                $email = trim($row['email'] ?? '');

                if (empty($name) || empty($email)) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Missing required field: name or email'];
                    $skipped++;
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Invalid email: ' . $email];
                    $skipped++;
                    continue;
                }

                $existing = $this->userModel->where('email', $email)->first();
                if ($existing) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Duplicate email: ' . $email];
                    $skipped++;
                    continue;
                }

                $countryId = null;
                $countryName = strtolower(trim($row['country'] ?? ''));
                if (!empty($countryName)) {
                    $countryId = $countryMap[$countryName] ?? null;
                    if ($countryId === null) {
                        $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Country not found: ' . ($row['country'] ?? '')];
                        $skipped++;
                        continue;
                    }
                }

                $insertData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => bin2hex(random_bytes(8)),
                    'phone' => trim($row['phone'] ?? ''),
                    'whatsapp' => trim($row['whatsapp'] ?? ''),
                    'country_id' => $countryId,
                    'user_type' => 'supplier',
                    'company_name' => trim($row['company_name'] ?? ''),
                    'company_introduction' => trim($row['company_introduction'] ?? ''),
                    'website' => trim($row['website'] ?? ''),
                    'city' => trim($row['city'] ?? ''),
                    'selling_products' => trim($row['selling_products'] ?? ''),
                    'membership_level' => 'free',
                    'lead_source' => 'csv_import',
                    'status' => 'pending',
                ];

                try {
                    $this->userModel->insert($insertData);
                    $results[] = ['row' => $rowNum, 'status' => 'Imported', 'reason' => $name . ' (' . $email . ')'];
                    $imported++;
                } catch (\Exception $e) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Insert error: ' . $e->getMessage()];
                    $skipped++;
                }
            }

            $this->userModel->skipValidation(false);

            $data['results'] = [
                'rows' => $results,
                'imported' => $imported,
                'skipped' => $skipped,
                'total' => count($rows) + count($malformed),
            ];
        }

        return view('admin/import/index', $data);
    }

    public function products()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $data = [
            'title' => 'Import Data',
            'user' => $user,
            'activeTab' => 'products',
            'results' => null,
        ];

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('csv_file');
            if (!$file || !$file->isValid() || $file->getExtension() !== 'csv') {
                $this->session->setFlashdata('error', 'Please upload a valid CSV file.');
                return redirect()->to('/admin/import/products');
            }

            [$headers, $rows, $malformed] = $this->parseCsv($file);
            if (empty($rows) && empty($malformed)) {
                $this->session->setFlashdata('error', 'CSV file is empty or has invalid format.');
                return redirect()->to('/admin/import/products');
            }

            [$countryMap, $categoryMap] = $this->buildLookupMaps();
            $results = $malformed;
            $imported = 0;
            $skipped = count($malformed);

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2;
                $name = trim($row['name'] ?? '');
                $supplierEmail = trim($row['supplier_email'] ?? '');
                $categoryName = strtolower(trim($row['category'] ?? ''));

                if (empty($name)) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Missing required field: name'];
                    $skipped++;
                    continue;
                }

                if (empty($supplierEmail)) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Missing required field: supplier_email'];
                    $skipped++;
                    continue;
                }

                $supplier = $this->userModel->where('email', $supplierEmail)->where('user_type', 'supplier')->first();
                if (!$supplier) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Supplier not found: ' . $supplierEmail];
                    $skipped++;
                    continue;
                }

                $categoryId = null;
                if (!empty($categoryName)) {
                    $categoryId = $categoryMap[$categoryName] ?? null;
                    if ($categoryId === null) {
                        $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Category not found: ' . ($row['category'] ?? '')];
                        $skipped++;
                        continue;
                    }
                }

                $slug = url_title($name, '-', true);
                $existingSlug = $this->productModel->where('slug', $slug)->first();
                if ($existingSlug) {
                    $slug = $slug . '-' . time() . '-' . $i;
                }

                $insertData = [
                    'supplier_id' => $supplier['id'],
                    'category_id' => $categoryId,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => trim($row['description'] ?? ''),
                    'specifications' => trim($row['specifications'] ?? ''),
                    'min_order_quantity' => trim($row['min_order_quantity'] ?? ''),
                    'min_order_unit' => trim($row['min_order_unit'] ?? ''),
                    'price_range' => trim($row['price_range'] ?? ''),
                    'supply_ability' => trim($row['supply_ability'] ?? ''),
                    'delivery_time' => trim($row['delivery_time'] ?? ''),
                    'packaging' => trim($row['packaging'] ?? ''),
                    'port' => trim($row['port'] ?? ''),
                    'payment_terms' => trim($row['payment_terms'] ?? ''),
                    'certifications' => trim($row['certifications'] ?? ''),
                    'is_featured' => 0,
                    'status' => 'active',
                ];

                try {
                    $this->productModel->insert($insertData);
                    $results[] = ['row' => $rowNum, 'status' => 'Imported', 'reason' => $name];
                    $imported++;
                } catch (\Exception $e) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Insert error: ' . $e->getMessage()];
                    $skipped++;
                }
            }

            $data['results'] = [
                'rows' => $results,
                'imported' => $imported,
                'skipped' => $skipped,
                'total' => count($rows) + count($malformed),
            ];
        }

        return view('admin/import/index', $data);
    }

    public function inquiries()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        $data = [
            'title' => 'Import Data',
            'user' => $user,
            'activeTab' => 'inquiries',
            'results' => null,
        ];

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('csv_file');
            if (!$file || !$file->isValid() || $file->getExtension() !== 'csv') {
                $this->session->setFlashdata('error', 'Please upload a valid CSV file.');
                return redirect()->to('/admin/import/inquiries');
            }

            [$headers, $rows, $malformed] = $this->parseCsv($file);
            if (empty($rows) && empty($malformed)) {
                $this->session->setFlashdata('error', 'CSV file is empty or has invalid format.');
                return redirect()->to('/admin/import/inquiries');
            }

            [$countryMap, $categoryMap] = $this->buildLookupMaps();
            $results = $malformed;
            $imported = 0;
            $skipped = count($malformed);

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2;
                $title = trim($row['title'] ?? '');
                $buyerEmail = trim($row['buyer_email'] ?? '');

                if (empty($title)) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Missing required field: title'];
                    $skipped++;
                    continue;
                }

                if (!empty($buyerEmail)) {
                    $existing = $this->inquiryModel->where('buyer_email', $buyerEmail)->where('title', $title)->first();
                    if ($existing) {
                        $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Duplicate: ' . $buyerEmail . ' + ' . $title];
                        $skipped++;
                        continue;
                    }
                }

                $countryId = null;
                $countryName = strtolower(trim($row['country'] ?? ''));
                if (!empty($countryName)) {
                    $countryId = $countryMap[$countryName] ?? null;
                    if ($countryId === null) {
                        $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Country not found: ' . ($row['country'] ?? '')];
                        $skipped++;
                        continue;
                    }
                }

                $categoryId = null;
                $categoryName = strtolower(trim($row['category'] ?? ''));
                if (!empty($categoryName)) {
                    $categoryId = $categoryMap[$categoryName] ?? null;
                    if ($categoryId === null) {
                        $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Category not found: ' . ($row['category'] ?? '')];
                        $skipped++;
                        continue;
                    }
                }

                $insertData = [
                    'category_id' => $categoryId,
                    'country_id' => $countryId,
                    'title' => $title,
                    'description' => trim($row['description'] ?? ''),
                    'product_name' => trim($row['product_name'] ?? ''),
                    'quantity' => trim($row['quantity'] ?? ''),
                    'unit' => trim($row['unit'] ?? ''),
                    'target_price' => trim($row['target_price'] ?? ''),
                    'buyer_name' => trim($row['buyer_name'] ?? ''),
                    'buyer_email' => $buyerEmail,
                    'buyer_phone' => trim($row['buyer_phone'] ?? ''),
                    'buyer_whatsapp' => trim($row['buyer_whatsapp'] ?? ''),
                    'buyer_company' => trim($row['buyer_company'] ?? ''),
                    'shipping_terms' => trim($row['shipping_terms'] ?? ''),
                    'payment_terms' => trim($row['payment_terms'] ?? ''),
                    'destination_port' => trim($row['destination_port'] ?? ''),
                    'is_featured' => 0,
                    'status' => 'active',
                ];

                try {
                    $this->inquiryModel->insert($insertData);
                    $results[] = ['row' => $rowNum, 'status' => 'Imported', 'reason' => $title];
                    $imported++;
                } catch (\Exception $e) {
                    $results[] = ['row' => $rowNum, 'status' => 'Skipped', 'reason' => 'Insert error: ' . $e->getMessage()];
                    $skipped++;
                }
            }

            $data['results'] = [
                'rows' => $results,
                'imported' => $imported,
                'skipped' => $skipped,
                'total' => count($rows) + count($malformed),
            ];
        }

        return view('admin/import/index', $data);
    }
}
