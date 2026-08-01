<?php

namespace App\Models;

use CodeIgniter\Model;

class BuyerInquiryModel extends Model
{
    protected $table = 'buyer_inquiries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id', 'category_id', 'country_id', 'title', 'slug', 'description',
        'product_name', 'quantity', 'unit', 'target_price', 'buyer_name',
        'buyer_email', 'buyer_phone', 'buyer_whatsapp', 'buyer_company',
        'shipping_terms', 'payment_terms', 'destination_port',
        'validity_date', 'inquiry_date', 'attachment',
        'is_featured', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowCallbacks = true;

    /**
     * Slugs are generated once, on insert, and never regenerated.
     *
     * There is deliberately no $beforeUpdate entry: regenerating a slug when a
     * title is edited would break every already-indexed URL and inbound link for
     * that row, and the canonical URL no longer carries an id to fall back on.
     * UserModel does the same thing for the same reason. The trade-off is that a
     * slug can drift from its title, which is cosmetic.
     */
    protected $beforeInsert = ['generateSlug'];

    /**
     * Populate slug on insert, resolving collisions with a numeric suffix.
     *
     * Lives here rather than in the four controllers that create inquiries
     * (Contact::submit, Dashboard::buyerAddInquiry, Dashboard::addInquiry,
     * AdminImport::inquiries) so that all of them are covered without edits.
     */
    protected function generateSlug(array $data): array
    {
        if (! empty($data['data']['slug'])) {
            return $data;
        }

        helper('inquiry');

        // Title is not a required field on the public RFQ form, so fall through
        // to other identifying fields before giving up on a generic base.
        $base = inquiry_slugify($data['data']['title'] ?? '');

        if ($base === '') {
            $base = inquiry_slugify($data['data']['product_name'] ?? '');
        }

        if ($base === '') {
            $base = inquiry_slugify($data['data']['buyer_company'] ?? '');
        }

        if ($base === '') {
            $base = 'buyer-inquiry';
        }

        $data['data']['slug'] = $this->uniqueSlug($base);

        return $data;
    }

    /**
     * Append -2, -3 ... until the slug is free. This is the runtime dedupe that
     * keeps the UNIQUE index on slug satisfied for every future insert.
     *
     * Uses a fresh connection rather than $this->where() on purpose: this runs
     * inside a beforeInsert callback, and touching the model's own query builder
     * mid-insert corrupts its state. UserModel::generateSlug() does the same, and
     * it is load-bearing rather than stylistic.
     */
    public function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $base = $base !== '' ? $base : 'buyer-inquiry';
        $db   = \Config\Database::connect();

        $slug = $base;
        $i    = 2;

        while (true) {
            $builder = $db->table($this->table)->where('slug', $slug);

            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }

            if ($builder->countAllResults() === 0) {
                return $slug;
            }

            $slug = $base . '-' . $i;
            $i++;
        }
    }

    /**
     * uniqueSlug() is check-then-insert, not atomic, so two concurrent inserts of
     * the same title can both pick the same slug and one will lose on the UNIQUE
     * index. Retry once with the slug cleared so the callback re-derives it
     * against now-current state.
     *
     * Signature must match CodeIgniter\BaseModel::insert() exactly or PHP raises a
     * fatal incompatible-signature error at class load.
     */
    public function insert($row = null, bool $returnID = true)
    {
        try {
            return parent::insert($row, $returnID);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            $message = $e->getMessage();

            if (stripos($message, 'Duplicate entry') === false || stripos($message, 'slug') === false) {
                throw $e;
            }

            if (is_array($row)) {
                unset($row['slug']);
            }

            return parent::insert($row, $returnID);
        }
    }

    public function getInquiryBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    public function getActiveInquiries($limit = null)
    {
        $builder = $this->where('status', 'active')->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    /**
     * $excludeId lets a detail page ask for related inquiries without getting
     * itself back. Without it the caller silently receives one fewer usable row
     * than it asked for, because the view has to filter the current inquiry out.
     */
    public function getInquiriesByCategory($categoryId, $limit = null, $excludeId = null)
    {
        $builder = $this->where('category_id', $categoryId)->where('status', 'active');
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getInquiriesByCountry($countryId, $limit = null)
    {
        $builder = $this->where('country_id', $countryId)->where('status', 'active');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->findAll();
    }

    public function getInquiriesByUser($userId)
    {
        return $this->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
    }

    public function getFeaturedInquiries($limit = 10)
    {
        return $this->where('is_featured', 1)->where('status', 'active')->limit($limit)->findAll();
    }

    public function searchInquiries($keyword)
    {
        return $this->like('title', $keyword)
            ->orLike('product_name', $keyword)
            ->orLike('description', $keyword)
            ->where('status', 'active')
            ->findAll();
    }

    public function getInquiryWithRelations($id)
    {
        $inquiry = $this->find($id);
        if ($inquiry) {
            $categoryModel = new CategoryModel();
            $countryModel = new CountryModel();
            $inquiry['category'] = $categoryModel->find($inquiry['category_id']);
            $inquiry['country'] = $countryModel->find($inquiry['country_id']);
        }
        return $inquiry;
    }
}
