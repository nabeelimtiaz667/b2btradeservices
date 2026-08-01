<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillBuyerInquirySlugs extends Migration
{
    private const TABLE = 'buyer_inquiries';

    public function up()
    {
        helper('inquiry');

        $db = \Config\Database::connect();

        // Drop any cached column list before the guard below. getFieldNames()
        // caches per connection, and the migration that adds `slug` runs on the
        // same connection in the same process, so the cache can predate it.
        $db->resetDataCache();

        if (! in_array('slug', $db->getFieldNames(self::TABLE), true)) {
            return;
        }

        // Seed the taken-set from anything already populated, so a re-run (or a
        // run after new rows have arrived) never collides with itself. This is
        // what makes the migration idempotent and therefore usable as a repair
        // tool: delete its row from `migrations` and run it again.
        $taken = [];

        $existing = $db->table(self::TABLE)
            ->select('slug')
            ->where('slug IS NOT NULL', null, false)
            ->where('slug !=', '')
            ->get()
            ->getResultArray();

        foreach ($existing as $row) {
            $taken[$row['slug']] = true;
        }

        $rows = $db->table(self::TABLE)
            ->select('id, title, product_name')
            ->groupStart()->where('slug', null)->orWhere('slug', '')->groupEnd()
            ->orderBy('id', 'ASC') // lowest id keeps the bare slug
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $base = inquiry_slugify($row['title']);

            if ($base === '') {
                $base = inquiry_slugify($row['product_name'] ?? '');
            }

            if ($base === '') {
                $base = 'buyer-inquiry';
            }

            $slug = $base;
            $i    = 2;

            while (isset($taken[$slug])) {
                $slug = $base . '-' . $i;
                $i++;
            }

            $taken[$slug] = true;

            // Raw builder rather than the model: buyer_inquiries.updated_at has
            // no ON UPDATE CURRENT_TIMESTAMP, so this leaves all 470 timestamps
            // untouched. Going through the model would churn every one of them.
            $db->table(self::TABLE)->where('id', $row['id'])->update(['slug' => $slug]);
        }
    }

    public function down()
    {
        // Data-only migration. AddSlugToBuyerInquiries::down() drops the column
        // along with its contents, so there is nothing meaningful to reverse.
    }
}
