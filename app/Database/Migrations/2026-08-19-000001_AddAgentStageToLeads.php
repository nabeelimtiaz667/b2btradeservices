<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds the same agent-assignment and pipeline-stage tracking `users` already
 * has (`assigned_agent_id`, `lead_stage` from AddLeadFieldsToUsers) to the
 * popup-capture `leads` table, per owner's explicit instruction to mirror
 * both fields exactly -- including the same `lead_stage` enum value list,
 * even though `leads.status` was deliberately renamed away from a competing
 * 'new' value in an earlier migration. `lead_stage` here is a different,
 * genuinely CRM-pipeline concept the owner wants replicated verbatim, not a
 * naming collision this migration is introducing.
 *
 * `assigned_agent_id` is an application-level reference to `users.id` where
 * `user_type='agent'` -- same convention as `users.assigned_agent_id`, no DB
 * foreign key (this codebase doesn't use FK constraints elsewhere either).
 */
class AddAgentStageToLeads extends Migration
{
    public function up()
    {
        $this->forge->addColumn('leads', [
            'assigned_agent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status',
            ],
            'lead_stage' => [
                'type'       => 'ENUM',
                'constraint' => ['new', 'trying_to_connect', 'connected_talking', 'services_pitched', 'interested_premium', 'contract_sent', 'not_interested', 'lead_lost'],
                'default'    => 'new',
                'after'      => 'assigned_agent_id',
            ],
        ]);

        $this->db->query('CREATE INDEX idx_leads_assigned_agent ON leads(assigned_agent_id)');
        $this->db->query('CREATE INDEX idx_leads_lead_stage ON leads(lead_stage)');
    }

    public function down()
    {
        $this->forge->dropColumn('leads', ['assigned_agent_id', 'lead_stage']);
    }
}
