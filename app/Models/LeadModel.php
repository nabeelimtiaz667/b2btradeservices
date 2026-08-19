<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * `leads` — step-1 captures from the popup CTA (name/email/phone/type), unrelated
 * to the `lead_stage`/`LeadActivityModel`/`LeadNoteModel` CRM fields already on
 * `users`. No FK to `users`; see .claude/plans/T-29-lead-capture.md for the full
 * design and status lifecycle this model implements.
 */
class LeadModel extends Model
{
    protected $table            = 'leads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_type',
        'name',
        'email',
        'phone',
        'phone_code',
        'whatsapp',
        'status',
        'assigned_agent_id',
        'lead_stage',
        'verification_token',
        'verification_token_expires_at',
        'verified_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        // Only referenced as the {id} placeholder target below -- present in
        // $row solely when a caller deliberately includes 'id' (e.g. the
        // admin edit form's update() call); cleanValidationRules() drops this
        // rule entirely when 'id' isn't in $row, so createLead()'s insert()
        // is unaffected. Required by this CI4 version's placeholder
        // mechanism: it validates the placeholder's own value using a rule
        // registered under that same field name before substituting it in.
        'id'        => 'permit_empty|is_natural_no_zero',
        'user_type' => 'required|in_list[supplier,buyer]',
        'name'      => 'required|min_length[2]|max_length[255]',
        'email'     => 'required|valid_email|is_unique[leads.email,id,{id}]',
        'phone'     => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'user_type' => [
            'required' => 'Please select Supplier or Buyer.',
            'in_list'  => 'Please select Supplier or Buyer.',
        ],
        'name' => [
            'required'   => 'Name is required.',
            'min_length' => 'Name must be at least 2 characters.',
        ],
        'email' => [
            'required'    => 'Email is required.',
            'valid_email' => 'Please enter a valid email address.',
            'is_unique'   => 'This email has already been submitted.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Token lifetime. Only ever checked while status = 'popup_form_filled' —
     * once email-verified the same link stays valid indefinitely (see plan:
     * "Timezone & expiry safety" and "Status lifecycle" sections for why).
     */
    protected const TOKEN_TTL_DAYS = 7;

    /**
     * Status values, named for the event each one represents rather than
     * generic lifecycle words -- see the rename note in the
     * 2026-08-16 migration for why ('new' collided in meaning with the
     * unrelated `users.lead_stage` CRM field, which also has a 'new').
     */
    private const STATUS_FORM_FILLED = 'popup_form_filled';
    private const STATUS_EMAIL_VERIFIED = 'email_verified';
    private const STATUS_ACCOUNT_REGISTERED = 'account_registered';

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function findByToken(string $token): ?array
    {
        if (empty($token)) {
            return null;
        }

        return $this->where('verification_token', $token)->first();
    }

    /**
     * Insert a brand-new lead (status 'popup_form_filled') with a fresh
     * verification token. Caller is responsible for confirming the email isn't
     * already a `users` row first -- this model only knows about the `leads`
     * table.
     */
    public function createLead(array $data): int|false
    {
        $token = $this->generateToken();

        $insertId = $this->insert([
            'user_type'                     => $data['user_type'],
            'name'                          => $data['name'],
            'email'                         => $data['email'],
            'phone'                         => $data['phone'] ?? null,
            'phone_code'                    => $data['phone_code'] ?? null,
            'whatsapp'                      => !empty($data['whatsapp']) ? 1 : 0,
            'status'                        => self::STATUS_FORM_FILLED,
            'verification_token'            => $token,
            'verification_token_expires_at' => $this->newExpiry(),
        ]);

        return $insertId ? $insertId : false;
    }

    /**
     * Update an existing 'popup_form_filled' lead's contact fields and reissue
     * a fresh token + expiry -- this is what makes an expired, never-verified
     * link self-healing: the visitor just fills the popup out again with the
     * same email.
     */
    public function reissueForNewLead(int $id, array $data): bool
    {
        return $this->update($id, [
            'user_type'                     => $data['user_type'],
            'name'                          => $data['name'],
            'phone'                         => $data['phone'] ?? null,
            'phone_code'                    => $data['phone_code'] ?? null,
            'whatsapp'                      => !empty($data['whatsapp']) ? 1 : 0,
            'verification_token'            => $this->generateToken(),
            'verification_token_expires_at' => $this->newExpiry(),
        ]);
    }

    /**
     * Update an already-verified lead's contact fields only. Token/status/email
     * are untouched -- email is the link's proof of identity, never editable.
     */
    public function updateContactOnly(int $id, array $data): bool
    {
        return $this->update($id, [
            'user_type'  => $data['user_type'],
            'name'       => $data['name'],
            'phone'      => $data['phone'] ?? null,
            'phone_code' => $data['phone_code'] ?? null,
            'whatsapp'   => !empty($data['whatsapp']) ? 1 : 0,
        ]);
    }

    public function markEmailVerified(int $id): bool
    {
        return $this->update($id, [
            'status'      => self::STATUS_EMAIL_VERIFIED,
            'verified_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public function markAccountRegistered(int $id): bool
    {
        return $this->update($id, [
            'status' => self::STATUS_ACCOUNT_REGISTERED,
        ]);
    }

    public function isPopupFormFilled(array $lead): bool
    {
        return $lead['status'] === self::STATUS_FORM_FILLED;
    }

    public function isEmailVerified(array $lead): bool
    {
        return $lead['status'] === self::STATUS_EMAIL_VERIFIED;
    }

    public function isAccountRegistered(array $lead): bool
    {
        return $lead['status'] === self::STATUS_ACCOUNT_REGISTERED;
    }

    /**
     * Only meaningful while status = 'popup_form_filled'. An 'email_verified'
     * lead's link has no expiry -- see plan's "Status lifecycle" table.
     */
    public function isTokenExpired(array $lead): bool
    {
        if (empty($lead['verification_token_expires_at'])) {
            return true;
        }

        return $lead['verification_token_expires_at'] < gmdate('Y-m-d H:i:s');
    }

    /**
     * Filtered, paginated listing for the admin "Popup Leads" page. Deliberately
     * separate from `UserModel::getLeads()` -- that method lists `users` rows
     * being nurtured through the CRM `lead_stage` pipeline; this one lists raw
     * `leads` table captures, an unrelated concept that happens to share the
     * word "lead". See .claude/plans/T-29-lead-capture.md.
     */
    public function getPopupLeads(array $filters = [], int $perPage = 25, int $page = 1): array
    {
        $builder = $this;

        if (!empty($filters['user_type'])) {
            $builder = $builder->where('user_type', $filters['user_type']);
        }

        if (!empty($filters['status'])) {
            $builder = $builder->where('status', $filters['status']);
        }

        if (!empty($filters['name'])) {
            $builder = $builder->like('name', $filters['name']);
        }

        if (!empty($filters['email'])) {
            $builder = $builder->like('email', $filters['email']);
        }

        if (!empty($filters['phone'])) {
            $builder = $builder->like('phone', $filters['phone']);
        }

        if (isset($filters['whatsapp']) && $filters['whatsapp'] !== '') {
            $builder = $builder->where('whatsapp', $filters['whatsapp']);
        }

        if (!empty($filters['date_from'])) {
            $builder = $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder = $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir   = $filters['sort_dir'] ?? 'DESC';
        $allowedSorts = ['name', 'email', 'user_type', 'status', 'created_at', 'verified_at'];
        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'created_at';
        }
        $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $builder = $builder->orderBy($sortField, $sortDir);

        $total = $builder->countAllResults(false);
        $leads = $builder->limit($perPage, ($page - 1) * $perPage)->findAll();

        return [
            'leads'       => $leads,
            'total'       => $total,
            'perPage'     => $perPage,
            'currentPage' => $page,
            'totalPages'  => max(1, (int) ceil($total / $perPage)),
        ];
    }

    protected function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * UTC, DATETIME-compatible string. Always PHP's clock (gmdate), never MySQL's
     * NOW()/CURRENT_TIMESTAMP -- see plan's "Timezone & expiry safety" section.
     */
    protected function newExpiry(): string
    {
        return gmdate('Y-m-d H:i:s', strtotime('+' . self::TOKEN_TTL_DAYS . ' days'));
    }
}
