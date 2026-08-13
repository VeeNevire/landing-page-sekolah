<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_id')->constrained('pkl_placements')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('aktivitas');
            $table->string('keterangan', 500)->nullable();
            $table->string('foto_path', 500)->nullable();
            $table->string('foto_name', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_pembimbing')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_activities');
    }
};
