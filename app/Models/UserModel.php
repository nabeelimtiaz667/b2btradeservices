<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uid',
        'name',
        'email',
        'password',
        'phone',
        'phone_code',
        'whatsapp',
        'country_id',
        'user_type',
        'selling_products',
        'buying_products',
        'requirement',
        'company_name',
        'slug',
        'company_logo',
        'banner_image',
        'banner_image_2',
        'banner_image_3',
        'company_introduction',
        'website',
        'city',
        'membership_level',
        'lead_stage',
        'lead_source',
        'assigned_agent_id',
        'landing_page_url',
        'last_login_at',
        'last_login_ip',
        'last_device_type',
        'department',
        'is_featured',
        'status',
        'reset_token',
        'reset_token_expires',
    ];
    // 'featured_set' deliberately NOT in $allowedFields -- drove manual
    // pinning into a specific Top Suppliers carousel set, removed
    // 2026-08-23 (Top Suppliers is now fully automatic). Column stays in
    // the DB (harmless, unused) rather than dropping it; nothing should
    // write to it anymore.
    // 'profile_view_count' deliberately NOT in $allowedFields -- system-managed
    // popularity counter (see Supplier::profile(), Pages.php's Top Suppliers
    // ranking), same reasoning as ProductModel::$view_count: kept out of mass
    // assignment, only ever touched via a raw atomic increment.

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'      => 'required|min_length[2]|max_length[255]',
        'email'     => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password'  => 'permit_empty|min_length[6]',
        'user_type' => 'permit_empty|in_list[supplier,buyer,admin,agent]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Name is required.',
            'min_length' => 'Name must be at least 2 characters.',
        ],
        'email' => [
            'required'    => 'Email is required.',
            'valid_email' => 'Please enter a valid email address.',
            'is_unique'   => 'This email is already registered.',
        ],
        'password' => [
            'required'   => 'Password is required.',
            'min_length' => 'Password must be at least 6 characters.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword', 'generateUID', 'generateSlug'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    protected function generateUID(array $data)
    {
        if (empty($data['data']['uid'])) {
            $type = $data['data']['user_type'] ?? 'buyer';
            $prefix = match ($type) {
                'supplier' => 'S',
                'buyer' => 'B',
                'admin' => 'ADM',
                'agent' => 'A',
                default => 'USR',
            };

            $lastUser = $this->selectMax('id')->first();
            $nextId = ($lastUser['id'] ?? 0) + 1;
            $data['data']['uid'] = $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }

        return $data;
    }

    protected function generateSlug(array $data)
    {
        if (empty($data['data']['slug'])) {
            $name = $data['data']['company_name'] ?? $data['data']['name'] ?? '';
            if (!empty($name)) {
                $base = strtolower(trim($name));
                $base = preg_replace('/[^a-z0-9]+/', '-', $base);
                $base = trim($base, '-');
                if (!empty($base)) {
                    $slug = $base;
                    $i = 2;
                    $db = \Config\Database::connect();
                    while ($db->table('users')->where('slug', $slug)->countAllResults() > 0) {
                        $slug = $base . '-' . $i;
                        $i++;
                    }
                    $data['data']['slug'] = $slug;
                }
            }
        }
        return $data;
    }

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function findByUid(string $uid)
    {
        return $this->where('uid', $uid)->first();
    }

    public function getLeads(array $filters = [], int $perPage = 25, int $page = 1)
    {
        $builder = $this->whereIn('user_type', ['supplier', 'buyer']);

        if (!empty($filters['lead_type'])) {
            $builder->where('user_type', $filters['lead_type']);
        }

        if (!empty($filters['uid'])) {
            $builder->like('uid', $filters['uid']);
        }

        if (!empty($filters['name'])) {
            $builder->like('name', $filters['name']);
        }

        if (!empty($filters['country_id'])) {
            $builder->where('country_id', $filters['country_id']);
        }

        if (!empty($filters['region'])) {
            $regionMapping = $this->getRegionMapping();
            if (isset($regionMapping[$filters['region']])) {
                $codes = $regionMapping[$filters['region']];
                $countryModel = new CountryModel();
                $regionCountries = $countryModel->whereIn('code', $codes)->findAll();
                $regionCountryIds = array_column($regionCountries, 'id');
                if (!empty($regionCountryIds)) {
                    $builder->whereIn('country_id', $regionCountryIds);
                }
            }
        }

        if (!empty($filters['phone'])) {
            $builder->like('phone', $filters['phone']);
        }

        if (!empty($filters['whatsapp']) && $filters['whatsapp'] !== '') {
            $builder->where('whatsapp', $filters['whatsapp']);
        }

        if (!empty($filters['email'])) {
            $builder->where('email', $filters['email']);
        }

        if (!empty($filters['membership_level'])) {
            $levels = explode(',', $filters['membership_level']);
            if (count($levels) > 1) {
                $builder->whereIn('membership_level', $levels);
            } else {
                $builder->where('membership_level', $filters['membership_level']);
            }
        }

        if (!empty($filters['lead_stage'])) {
            $builder->where('lead_stage', $filters['lead_stage']);
        }

        if (!empty($filters['assigned_agent_id'])) {
            $builder->where('assigned_agent_id', $filters['assigned_agent_id']);
        }

        if (!empty($filters['lead_source'])) {
            $builder->like('lead_source', $filters['lead_source']);
        }

        if (!empty($filters['products'])) {
            $builder->groupStart()
                ->like('selling_products', $filters['products'])
                ->orLike('buying_products', $filters['products'])
                ->groupEnd();
        }

        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'DESC';
        $allowedSorts = ['uid', 'name', 'email', 'user_type', 'membership_level', 'lead_stage', 'created_at', 'last_login_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }
        $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $builder->orderBy($sortField, $sortDir);

        $total = $builder->countAllResults(false);
        $leads = $builder->limit($perPage, ($page - 1) * $perPage)->findAll();

        return [
            'leads' => $leads,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    public function getAgents()
    {
        return $this->where('user_type', 'agent')->orderBy('name', 'ASC')->findAll();
    }

    public function getLeadStages()
    {
        return [
            'new' => 'New',
            'trying_to_connect' => 'Trying to Connect',
            'connected_talking' => 'Connected & Talking',
            'services_pitched' => 'Services Pitched',
            'interested_premium' => 'Interested in Premium Services', 
            'contract_sent' => 'Contract Sent',
            'not_interested' => 'Not Interested',
            'lead_lost' => 'Lead Lost',
        ];
    }

    public function getMembershipLevels()
    {
        return [
            'free' => 'Free',
            'starter' => 'Starter',
            'gold' => 'Gold',
            'platinum' => 'Platinum',
            'vip' => 'VIP',
        ];
    }

    public function getRegionMapping()
    {
        return [
            'China' => ['CN'],
            'United States' => ['US'],
            'Europe' => ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'SE', 'NO', 'DK', 'FI', 'PT', 'AT', 'CH', 'IE', 'PL', 'CZ', 'RO', 'HU', 'GR', 'BG', 'HR', 'SK', 'SI', 'LT', 'LV', 'EE', 'LU', 'MT', 'CY'],
            'Africa' => ['ZA', 'NG', 'KE', 'EG', 'GH', 'TZ', 'ET', 'UG', 'DZ', 'MA', 'TN', 'SN', 'CM', 'CI', 'AO', 'MZ', 'MG', 'CD', 'ZW', 'BW', 'RW', 'MW', 'ZM', 'NA', 'ML', 'BF', 'NE', 'TD', 'SO', 'LY', 'SD', 'SS', 'ER', 'DJ', 'KM', 'MU', 'SC', 'CV', 'ST', 'GA', 'GQ', 'CG', 'BI', 'BJ', 'TG', 'SL', 'LR', 'GW', 'GM', 'LS', 'SZ', 'MR'],
            'UAE' => ['AE'],
            'Caucasian Region' => ['GE', 'AM', 'AZ'],
        ];
    }
}
