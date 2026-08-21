<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds real popularity tracking for the homepage's "Top Products"/"Top
 * Suppliers" sections. Previously both were driven entirely by `is_featured`
 * -- a manual admin checkbox that defaults to unchecked on every new
 * product/supplier -- so nothing new could ever surface there without
 * someone remembering to flag it by hand. These columns let Pages.php rank
 * by actual observed traffic instead. See CHANGELOG 2026-08-21.
 */
class AddViewCountsForTopRanking extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'view_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'is_featured',
            ],
        ]);

        $this->forge->addColumn('users', [
            'profile_view_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'is_featured',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'view_count');
        $this->forge->dropColumn('users', 'profile_view_count');
    }
}
