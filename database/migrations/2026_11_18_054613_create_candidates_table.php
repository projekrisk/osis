<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor_urut')->unique();
            $table->string('kode_paslon', 20)->unique();
            $table->string('ketua_nama');
            $table->string('ketua_kelas', 30);
            $table->string('wakil_nama');
            $table->string('wakil_kelas', 30);
            $table->text('visi');
            $table->text('misi');
            $table->string('foto_path')->nullable();
            $table->string('status', 20)->default('aktif'); // aktif, verifikasi, draft
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
