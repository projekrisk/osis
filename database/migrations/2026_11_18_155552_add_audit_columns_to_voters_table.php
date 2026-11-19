<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('receipt_code');
            $table->string('browser', 80)->nullable()->after('ip_address');
            $table->string('os', 80)->nullable()->after('browser');
            $table->string('device_type', 40)->nullable()->after('os');
            $table->text('user_agent')->nullable()->after('device_type');
        });
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'browser', 'os', 'device_type', 'user_agent']);
        });
    }
};
