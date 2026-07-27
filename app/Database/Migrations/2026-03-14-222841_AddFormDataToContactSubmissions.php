<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFormDataToContactSubmissions extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('contact_submissions');
        if (!in_array('form_data', $fields)) {
            $this->forge->addColumn('contact_submissions', [
                'form_data' => [
                    'type'  => 'TEXT',
                    'null'  => true,
                    'after' => 'admin_notes',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('contact_submissions', 'form_data');
    }
}
