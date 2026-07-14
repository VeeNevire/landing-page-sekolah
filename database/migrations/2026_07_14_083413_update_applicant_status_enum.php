<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('draft', 'submitted', 'verified', 'paid', 'rejected') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('draft', 'submitted', 'verified', 'payment_pending', 'paid', 'rejected') DEFAULT 'draft'");
    }
};
