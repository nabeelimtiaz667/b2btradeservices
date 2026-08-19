<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Notes on popup-capture `leads` rows. Mirrors `LeadNoteModel` (`lead_notes`,
 * notes on `users` rows) field-for-field and method-for-method, but against
 * `popup_leads_notes` / `leads.id` instead -- kept as a separate model/table
 * per owner's instruction, since a popup lead's id is not a users.id.
 */
class PopupLeadNoteModel extends Model
{
    protected $table            = 'popup_leads_notes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lead_id',
        'agent_user_id',
        'note',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getNotesByLead(int $leadId)
    {
        return $this->where('lead_id', $leadId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getNotesWithAgent(int $leadId)
    {
        $db = \Config\Database::connect();
        return $db->table('popup_leads_notes')
            ->select('popup_leads_notes.*, users.name as agent_name')
            ->join('users', 'users.id = popup_leads_notes.agent_user_id', 'left')
            ->where('popup_leads_notes.lead_id', $leadId)
            ->orderBy('popup_leads_notes.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getLatestNoteForLead(int $leadId)
    {
        return $this->where('lead_id', $leadId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function getLatestNotesForLeads(array $leadIds): array
    {
        if (empty($leadIds)) return [];
        $db = \Config\Database::connect();
        $rows = $db->query(
            "SELECT pln.lead_id, pln.note, pln.created_at FROM popup_leads_notes pln
             INNER JOIN (SELECT lead_id, MAX(id) as max_id FROM popup_leads_notes WHERE lead_id IN (" . implode(',', array_map('intval', $leadIds)) . ") GROUP BY lead_id) latest
             ON pln.id = latest.max_id"
        )->getResultArray();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['lead_id']] = $row['note'];
        }
        return $result;
    }
}
