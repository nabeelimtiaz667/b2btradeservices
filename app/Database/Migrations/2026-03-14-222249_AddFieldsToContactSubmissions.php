<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToContactSubmissions extends Migration
{
    public function up()
    {
        // Guarded because these columns already exist in databases that were
        // populated before this migration was recorded. A bare addColumn() here
        // fataled with "Duplicate column name 'country_id'", and CI4 aborts the
        // whole batch on the exception, so no later migration could ever run.
        // Same guard style as the sibling 2026-03-14-222841 migration.
        $db     = \Config\Database::connect();
        $fields = $db->getFieldNames('contact_submissions');
        $add    = [];

        if (! in_array('country_id', $fields, true)) {
            $add['country_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'phone',
            ];
        }

        if (! in_array('partnership', $fields, true)) {
            $add['partnership'] = [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'country_id',
            ];
        }

        if (! in_array('whatsapp', $fields, true)) {
            $add['whatsapp'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'partnership',
            ];
        }

        if ($add !== []) {
            $this->forge->addColumn('contact_submissions', $add);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('contact_submissions', ['country_id', 'partnership', 'whatsapp']);
    }
}
