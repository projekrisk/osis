<?php

namespace Database\Seeders;

use App\Models\ElectionSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OsisSeeder extends Seeder
{
    public function run(): void
    {
        ElectionSetting::current();

        User::firstOrCreate(
            ['email' => 'admin@sman1malingping.sch.id'],
            [
                'name' => 'Administrator OSIS',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'tps_id' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'panitia@sman1malingping.sch.id'],
            [
                'name' => 'Panitia OSIS',
                'password' => Hash::make('password'),
                'role' => 'panitia',
                'tps_id' => null,
            ]
        );
    }
}
