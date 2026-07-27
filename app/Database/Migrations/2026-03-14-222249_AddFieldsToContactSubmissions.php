<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToContactSubmissions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('contact_submissions', [
            'country_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'phone',
            ],
            'partnership' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'country_id',
            ],
            'whatsapp' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'partnership',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('contact_submissions', ['country_id', 'partnership', 'whatsapp']);
    }
}
