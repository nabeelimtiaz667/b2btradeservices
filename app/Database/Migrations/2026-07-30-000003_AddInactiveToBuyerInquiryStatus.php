<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInactiveToBuyerInquiryStatus extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // buyer_inquiries.status was enum('active','closed','pending','expired'),
        // but the admin listings screen offers an "Inactive" option
        // (app/Views/admin/settings/listings.php) and AdminSettings writes the
        // posted value straight through. sql_mode here has no STRICT flag, so
        // 'inactive' was silently coerced to '' — which fails every
        // status === 'active' check, making the inquiry vanish from all listings
        // and 404 on its detail URL.
        //
        // Widening rather than remapping: products.status is already
        // enum('active','inactive','pending'), so this aligns the two tables and
        // keeps the admin UI working with no behaviour change. Note the approval
        // queue uses 'pending', not 'inactive' — see Dashboard::buyerAddInquiry.
        $db->query(
            'ALTER TABLE `buyer_inquiries` MODIFY `status` '
            . "ENUM('active','inactive','closed','pending','expired') "
            . "NOT NULL DEFAULT 'active'"
        );

        // Repair anything already coerced to '' by a pre-fix admin click.
        $db->query("UPDATE `buyer_inquiries` SET `status` = 'inactive' WHERE `status` = ''");
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Map 'inactive' onto a value the narrower ENUM can hold before shrinking,
        // otherwise those rows would be coerced to '' on the way back down.
        $db->query("UPDATE `buyer_inquiries` SET `status` = 'pending' WHERE `status` = 'inactive'");

        $db->query(
            'ALTER TABLE `buyer_inquiries` MODIFY `status` '
            . "ENUM('active','closed','pending','expired') NOT NULL DEFAULT 'active'"
        );
    }
}
