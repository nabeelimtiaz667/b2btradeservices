<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadNotesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'lead_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'agent_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('lead_user_id');
        $this->forge->addKey('agent_user_id');
        $this->forge->createTable('lead_notes');
    }

    public function down()
    {
        $this->forge->dropTable('lead_notes');
    }
}
