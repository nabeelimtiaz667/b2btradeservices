<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class AddSlugToBuyerInquiries extends Migration
{
    private const TABLE      = 'buyer_inquiries';
    private const INDEX_NAME = 'buyer_inquiries_slug';

    public function up()
    {
        $db = \Config\Database::connect();

        // Guarded: migrations do not fully describe this schema. buyer_whatsapp,
        // inquiry_date and users.slug all exist in the live database with no
        // migration behind them, so assume any column may already be present.
        if (! in_array('slug', $db->getFieldNames(self::TABLE), true)) {
            $this->forge->addColumn(self::TABLE, [
                'slug' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'title',
                ],
            ]);

            // getFieldNames() caches per connection and Forge::addColumn() does
            // not invalidate it. Every migration in a single `spark migrate` run
            // shares one connection, so without this the *next* migration's
            // column guard reads a stale list, concludes slug does not exist and
            // silently skips its work.
            $db->resetDataCache();
        }

        if ($this->hasIndex($db)) {
            return;
        }

        // If the column arrived by drift and already holds duplicates, fail with
        // a message naming them rather than with a raw MySQL error mid-ALTER.
        $dupes = $db->query(
            'SELECT slug FROM `' . self::TABLE . '`'
            . " WHERE slug IS NOT NULL AND slug <> ''"
            . ' GROUP BY slug HAVING COUNT(*) > 1'
        )->getResultArray();

        if ($dupes !== []) {
            throw new RuntimeException(
                'Cannot add UNIQUE index on ' . self::TABLE . '.slug; duplicates present: '
                . implode(', ', array_column($dupes, 'slug'))
            );
        }

        // Deliberately indexed BEFORE the backfill runs. Every existing row is
        // slug = NULL, and MySQL/MariaDB do not treat NULLs as equal in a UNIQUE
        // index, so this is safe now. Doing it in this order means the database
        // polices uniqueness *during* the backfill instead of the backfill
        // having to be trusted afterwards. users.slug is already
        // varchar(255) NULL UNIQUE, so this matches existing practice here.
        $db->query('ALTER TABLE `' . self::TABLE . '` ADD UNIQUE KEY `' . self::INDEX_NAME . '` (`slug`)');
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($this->hasIndex($db)) {
            $db->query('ALTER TABLE `' . self::TABLE . '` DROP INDEX `' . self::INDEX_NAME . '`');
        }

        if (in_array('slug', $db->getFieldNames(self::TABLE), true)) {
            $this->forge->dropColumn(self::TABLE, 'slug');
        }
    }

    private function hasIndex($db): bool
    {
        foreach ($db->getIndexData(self::TABLE) as $index) {
            if ($index->name === self::INDEX_NAME) {
                return true;
            }
        }

        return false;
    }
}
