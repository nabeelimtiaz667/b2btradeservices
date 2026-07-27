<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsFeaturedToUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('users');
        if (!in_array('is_featured', $fields)) {
            $this->forge->addColumn('users', [
                'is_featured' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'null'       => false,
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'is_featured');
    }
}
