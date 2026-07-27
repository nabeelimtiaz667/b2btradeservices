<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBuyerInquiriesTable extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'country_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'quantity' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'target_price' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'buyer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'buyer_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'buyer_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'buyer_company' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'shipping_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'payment_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'destination_port' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'validity_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'closed', 'pending', 'expired'],
                'default' => 'active',
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('category_id');
        $this->forge->addKey('country_id');
        $this->forge->createTable('buyer_inquiries');
    }

    public function down()
    {
        $this->forge->dropTable('buyer_inquiries');
    }
}
