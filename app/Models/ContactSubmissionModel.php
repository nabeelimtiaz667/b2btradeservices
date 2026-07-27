<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactSubmissionModel extends Model
{
    protected $table = 'contact_submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'form_type', 'name', 'email', 'phone', 'country_id',
        'partnership', 'whatsapp', 'company',
        'industry', 'quantity', 'message', 'source_page',
        'source_id', 'status', 'admin_notes', 'form_data',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
