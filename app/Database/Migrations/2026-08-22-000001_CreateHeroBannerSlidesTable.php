<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Homepage hero banner (the `.banner-slider` at the top of index.php) moves
 * from 3 hardcoded <img> tags to an admin-managed table, so an admin can add,
 * reorder, retire, and restore slides without a code deploy. `status`
 * distinguishes currently-live slides from a "history" shelf (retired, not
 * deleted) -- there's no existing soft-delete pattern in this codebase to
 * follow, so this is a plain status column rather than CI4's built-in
 * useSoftDeletes, matching how status is already used everywhere else here
 * (products, suppliers, inquiries). See CHANGELOG 2026-08-22.
 *
 * This migration also does the one-time move of the 3 images currently
 * hardcoded in index.php (web-ban01/02/03.webp, all real files already in
 * public/assets/images/) into their own uploads/hero-banner directory and
 * seeds the matching rows -- keeping their original filenames, since they
 * predate the "unique generated name" rule that only applies to slides
 * uploaded through the new admin form from here on.
 */
class CreateHeroBannerSlidesTable extends Migration
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
            'image_filename' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'link_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'history'],
                'default' => 'active',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'sort_order']);
        $this->forge->createTable('hero_banner_slides');

        // Move the 3 currently-hardcoded images into their new home. Source
        // files are committed static assets, so this only ever copies (never
        // moves/deletes) -- if the destination directory or a file is
        // already there (e.g. a re-run), it's left alone rather than
        // overwritten.
        $sourceDir = FCPATH . 'assets/images/';
        $destDir = FCPATH . 'uploads/hero-banner/';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $seedSlides = [
            ['file' => 'web-ban01.webp', 'link' => 'premium-services', 'order' => 1],
            ['file' => 'web-ban02.webp', 'link' => 'premium-services', 'order' => 2],
            ['file' => 'web-ban03.webp', 'link' => 'buyers', 'order' => 3],
        ];

        $now = gmdate('Y-m-d H:i:s');
        $db = \Config\Database::connect();

        foreach ($seedSlides as $slide) {
            $source = $sourceDir . $slide['file'];
            $dest = $destDir . $slide['file'];

            if (is_file($source) && !is_file($dest)) {
                copy($source, $dest);
            }

            if (is_file($dest)) {
                $db->table('hero_banner_slides')->insert([
                    'image_filename' => $slide['file'],
                    'link_url' => $slide['link'],
                    'sort_order' => $slide['order'],
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        // Deliberately doesn't delete anything under uploads/hero-banner/ --
        // migrations here are one-way in practice (see BLOCKERS #17), and
        // silently deleting uploaded images on a rollback would be far worse
        // than leaving orphaned files behind.
        $this->forge->dropTable('hero_banner_slides');
    }
}
