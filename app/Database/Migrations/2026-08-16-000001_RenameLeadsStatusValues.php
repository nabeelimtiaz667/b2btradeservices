<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renames leads.status values to be self-explanatory instead of generic
 * lifecycle words -- 'new'/'verified'/'converted' read as ambiguous next to
 * the CRM lead_stage values already on `users` (which also has a 'new'),
 * whereas 'popup_form_filled'/'email_verified'/'account_registered' name the
 * actual event each status represents. See .claude/plans/T-29-lead-capture.md.
 *
 * ENUM only accepts values in its declared list, so a straight rename would
 * reject the old values mid-migration. Widen first (old values still valid),
 * migrate the data, then narrow to just the new values -- the standard-safe
 * way to rename a MySQL ENUM without a data loss window.
 */
class RenameLeadsStatusValues extends Migration
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE leads MODIFY COLUMN status " .
            "ENUM('new','verified','converted','popup_form_filled','email_verified','account_registered') " .
            "NOT NULL DEFAULT 'new'"
        );

        $this->db->query("UPDATE leads SET status = 'popup_form_filled' WHERE status = 'new'");
        $this->db->query("UPDATE leads SET status = 'email_verified' WHERE status = 'verified'");
        $this->db->query("UPDATE leads SET status = 'account_registered' WHERE status = 'converted'");

        $this->db->query(
            "ALTER TABLE leads MODIFY COLUMN status " .
            "ENUM('popup_form_filled','email_verified','account_registered') " .
            "NOT NULL DEFAULT 'popup_form_filled'"
        );
    }

    public function down()
    {
        $this->db->query(
            "ALTER TABLE leads MODIFY COLUMN status " .
            "ENUM('popup_form_filled','email_verified','account_registered','new','verified','converted') " .
            "NOT NULL DEFAULT 'popup_form_filled'"
        );

        $this->db->query("UPDATE leads SET status = 'new' WHERE status = 'popup_form_filled'");
        $this->db->query("UPDATE leads SET status = 'verified' WHERE status = 'email_verified'");
        $this->db->query("UPDATE leads SET status = 'converted' WHERE status = 'account_registered'");

        $this->db->query(
            "ALTER TABLE leads MODIFY COLUMN status " .
            "ENUM('new','verified','converted') NOT NULL DEFAULT 'new'"
        );
    }
}
