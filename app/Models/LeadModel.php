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
     * Token lifetime. Only ever checked while status = 'new' — once verified the
     * same link stays valid indefinitely (see plan: "Timezone & expiry safety" and
     * "Status lifecycle" sections for why).
     */
    protected const TOKEN_TTL_DAYS = 7;

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
     * Insert a brand-new lead (status 'new') with a fresh verification token.
     * Caller is responsible for confirming the email isn't already a `users` row
     * first -- this model only knows about the `leads` table.
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
            'status'                        => 'new',
            'verification_token'            => $token,
            'verification_token_expires_at' => $this->newExpiry(),
        ]);

        return $insertId ? $insertId : false;
    }

    /**
     * Update an existing 'new' lead's contact fields and reissue a fresh token +
     * expiry -- this is what makes an expired, never-verified link self-healing:
     * the visitor just fills the popup out again with the same email.
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

    public function markVerified(int $id): bool
    {
        return $this->update($id, [
            'status'      => 'verified',
            'verified_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public function markConverted(int $id): bool
    {
        return $this->update($id, [
            'status' => 'converted',
        ]);
    }

    public function isNew(array $lead): bool
    {
        return $lead['status'] === 'new';
    }

    public function isVerified(array $lead): bool
    {
        return $lead['status'] === 'verified';
    }

    public function isConverted(array $lead): bool
    {
        return $lead['status'] === 'converted';
    }

    /**
     * Only meaningful while status = 'new'. A 'verified' lead's link has no
     * expiry -- see plan's "Status lifecycle" table.
     */
    public function isTokenExpired(array $lead): bool
    {
        if (empty($lead['verification_token_expires_at'])) {
            return true;
        }

        return $lead['verification_token_expires_at'] < gmdate('Y-m-d H:i:s');
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
