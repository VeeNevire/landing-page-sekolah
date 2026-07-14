<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('draft', 'submitted', 'verified', 'paid', 'rejected') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('draft', 'submitted', 'verified', 'payment_pending', 'paid', 'rejected') DEFAULT 'draft'");
    }
};
