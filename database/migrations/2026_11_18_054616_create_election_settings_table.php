<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah')->default('SMA Negeri 1 Nusantara');
            $table->string('nama_kegiatan')->default('Pemilihan Ketua & Wakil Ketua OSIS');
            $table->string('tahun_ajaran', 20)->default('2026/2027');
            $table->string('status_pemilihan', 20)->default('berlangsung'); // persiapan, berlangsung, selesai
            $table->string('jadwal_mulai')->default('2026-11-18 07:30');
            $table->string('jadwal_selesai')->default('2026-11-18 15:00');
            $table->string('logo_path')->nullable();
            $table->boolean('require_token')->default(true);
            $table->boolean('allow_abstain')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_settings');
    }
};
