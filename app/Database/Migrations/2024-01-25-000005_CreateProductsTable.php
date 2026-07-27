<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
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
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'specifications' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'main_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'gallery_images' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'min_order_quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'min_order_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'price_range' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'supply_ability' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'delivery_time' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'packaging' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'port' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'payment_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'certifications' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'pending'],
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
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('category_id');
        $this->forge->addKey('slug');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
