<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadsTable extends Migration
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
            'user_type' => [
                'type'       => 'ENUM',
                'constraint' => ['supplier', 'buyer'],
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'whatsapp' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['new', 'verified', 'converted'],
                'default'    => 'new',
            ],
            // Not TIMESTAMP: MySQL silently converts TIMESTAMP values between UTC
            // storage and the session's time_zone on every read/write. These are
            // always written and compared as UTC strings from PHP's gmdate(), so
            // DATETIME (no conversion, stores the literal value) is what keeps that
            // consistent regardless of the DB session's timezone setting.
            'verification_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
            ],
            'verification_token_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('verification_token');
        $this->forge->createTable('leads');
    }

    public function down()
    {
        $this->forge->dropTable('leads');
    }
}
