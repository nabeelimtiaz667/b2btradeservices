<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Mirrors `lead_notes` (see CreateLeadNotesTable), but for popup-capture
 * `leads` rows instead of `users` rows -- per owner's instruction, a
 * separate table rather than reusing `lead_notes`, since a popup lead's `id`
 * is not a `users.id` and mixing the two would make `lead_user_id` lie about
 * which table it points at. `lead_id` (not `lead_user_id`) names that
 * distinction explicitly.
 */
class CreatePopupLeadsNotesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lead_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'agent_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'note' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('lead_id');
        $this->forge->addKey('agent_user_id');
        $this->forge->createTable('popup_leads_notes');
    }

    public function down()
    {
        $this->forge->dropTable('popup_leads_notes');
    }
}
