<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadNoteModel extends Model
{
    protected $table            = 'lead_notes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lead_user_id',
        'agent_user_id',
        'note',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getNotesByLead(int $leadUserId)
    {
        return $this->where('lead_user_id', $leadUserId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getNotesWithAgent(int $leadUserId)
    {
        $db = \Config\Database::connect();
        return $db->table('lead_notes')
            ->select('lead_notes.*, users.name as agent_name')
            ->join('users', 'users.id = lead_notes.agent_user_id', 'left')
            ->where('lead_notes.lead_user_id', $leadUserId)
            ->orderBy('lead_notes.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getLatestNoteForLead(int $leadUserId)
    {
        return $this->where('lead_user_id', $leadUserId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function getLatestNotesForLeads(array $leadIds): array
    {
        if (empty($leadIds)) return [];
        $db = \Config\Database::connect();
        $rows = $db->query(
            "SELECT ln.lead_user_id, ln.note, ln.created_at FROM lead_notes ln
             INNER JOIN (SELECT lead_user_id, MAX(id) as max_id FROM lead_notes WHERE lead_user_id IN (" . implode(',', array_map('intval', $leadIds)) . ") GROUP BY lead_user_id) latest
             ON ln.id = latest.max_id"
        )->getResultArray();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['lead_user_id']] = $row['note'];
        }
        return $result;
    }
}
