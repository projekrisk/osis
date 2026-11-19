<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 30)->unique();
            $table->string('nama');
            $table->string('kelas', 30);
            $table->foreignId('tps_id')->nullable()->constrained('tps')->nullOnDelete();
            $table->string('token', 16);
            $table->boolean('has_voted')->default(false);
            $table->timestamp('voted_at')->nullable();
            $table->string('receipt_code', 24)->nullable();
            $table->string('status', 20)->default('terverifikasi'); // terverifikasi, belum_aktivasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};
