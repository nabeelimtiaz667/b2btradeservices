<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Lets a hero banner slide's image come from either an upload (stored under
 * uploads/hero-banner/, `image_filename` holds just the filename) or a
 * direct external URL (`image_filename` holds the full URL as typed, used
 * verbatim with no trimming/formatting -- see AdminSettings::heroBanners()).
 * Reusing `image_filename` for both rather than adding a separate url column
 * since the two are mutually exclusive per row and every read site already
 * has to branch on `file_type` to know how to resolve it either way. See
 * CHANGELOG 2026-08-22.
 */
class AddFileTypeToHeroBannerSlides extends Migration
{
    public function up()
    {
        $this->forge->addColumn('hero_banner_slides', [
            'file_type' => [
                'type' => 'ENUM',
                'constraint' => ['upload', 'url'],
                'default' => 'upload',
                'after' => 'image_filename',
            ],
        ]);
        // DEFAULT 'upload' on ADD COLUMN already backfills every existing
        // row correctly -- the 3 seeded slides are all real uploaded files.
    }

    public function down()
    {
        $this->forge->dropColumn('hero_banner_slides', 'file_type');
    }
}
