<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Lets the homepage's Top Products/Top Suppliers carousel have more than one
 * rotating set. Each set still gets exactly one admin-pinned (is_featured)
 * item -- featured_set records *which* set a pinned row belongs to (1-based,
 * matching the set number shown in the admin UI). NULL means "not assigned
 * to a set" (either never featured, or featured before this column existed).
 * See CHANGELOG 2026-08-21.
 */
class AddFeaturedSetForTopCarousels extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'featured_set' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'is_featured',
            ],
        ]);

        $this->forge->addColumn('users', [
            'featured_set' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'is_featured',
            ],
        ]);

        // Existing is_featured=1 rows predate sets entirely -- assign them to
        // set 1 so they keep behaving exactly as they did under the
        // single-pin-cap logic shipped earlier today, until an admin
        // reassigns them.
        $this->db->query("UPDATE products SET featured_set = 1 WHERE is_featured = 1");
        $this->db->query("UPDATE users SET featured_set = 1 WHERE is_featured = 1");
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'featured_set');
        $this->forge->dropColumn('users', 'featured_set');
    }
}
