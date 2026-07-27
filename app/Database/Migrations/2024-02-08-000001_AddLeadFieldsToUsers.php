<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLeadFieldsToUsers extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE users MODIFY COLUMN user_type ENUM('supplier','buyer','admin','agent') NOT NULL DEFAULT 'buyer'");

        $this->db->query("ALTER TABLE users MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");

        $this->forge->addColumn('users', [
            'uid' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'id',
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'requirement',
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'company_name',
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'website',
            ],
            'membership_level' => [
                'type' => 'ENUM',
                'constraint' => ['free', 'silver', 'gold', 'platinum', 'vip'],
                'default' => 'free',
                'after' => 'city',
            ],
            'lead_stage' => [
                'type' => 'ENUM',
                'constraint' => ['new', 'trying_to_connect', 'connected_talking', 'services_pitched', 'interested_premium', 'contract_sent', 'not_interested', 'lead_lost'],
                'default' => 'new',
                'after' => 'membership_level',
            ],
            'lead_source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'lead_stage',
            ],
            'assigned_agent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'lead_source',
            ],
            'landing_page_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'assigned_agent_id',
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'landing_page_url',
            ],
            'last_login_ip' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'after' => 'last_login_at',
            ],
            'last_device_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'last_login_ip',
            ],
        ]);

        $this->db->query("CREATE UNIQUE INDEX idx_users_uid ON users(uid)");
        $this->db->query("CREATE INDEX idx_users_lead_stage ON users(lead_stage)");
        $this->db->query("CREATE INDEX idx_users_assigned_agent ON users(assigned_agent_id)");
        $this->db->query("CREATE INDEX idx_users_membership ON users(membership_level)");
    }

    public function down()
    {
        $this->forge->dropColumn('users', [
            'uid', 'company_name', 'website', 'city', 'membership_level',
            'lead_stage', 'lead_source', 'assigned_agent_id',
            'landing_page_url', 'last_login_at', 'last_login_ip', 'last_device_type',
        ]);

        $this->db->query("ALTER TABLE users MODIFY COLUMN user_type ENUM('supplier','buyer','admin') NOT NULL DEFAULT 'buyer'");
        $this->db->query("ALTER TABLE users MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
}
