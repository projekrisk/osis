<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Tps;
use App\Models\User;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OsisSeeder extends Seeder
{
    public function run(): void
    {
        ElectionSetting::current();

        $tpsData = [
            ['kode_tps' => 'TPS-01', 'nama_tps' => 'TPS 01 — Aula Utama', 'lokasi' => 'Gedung A Lantai 1', 'status' => 'aktif'],
            ['kode_tps' => 'TPS-02', 'nama_tps' => 'TPS 02 — Lab Komputer', 'lokasi' => 'Gedung B Lantai 2', 'status' => 'aktif'],
            ['kode_tps' => 'TPS-03', 'nama_tps' => 'TPS 03 — Perpustakaan', 'lokasi' => 'Gedung C Lantai 1', 'status' => 'aktif'],
        ];

        $tpsMap = [];
        foreach ($tpsData as $t) {
            $tps = Tps::firstOrCreate(['kode_tps' => $t['kode_tps']], $t);
            $tpsMap[$t['kode_tps']] = $tps;
        }

        User::firstOrCreate(
            ['email' => 'admin@osis.sch.id'],
            [
                'name' => 'Administrator OSIS',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'tps_id' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'panitia@osis.sch.id'],
            [
                'name' => 'Panitia TPS 01',
                'password' => Hash::make('password'),
                'role' => 'panitia',
                'tps_id' => $tpsMap['TPS-01']->id ?? null,
            ]
        );

        $candidates = [
            [
                'nomor_urut' => 1,
                'kode_paslon' => 'PASLON-01',
                'ketua_nama' => 'Raka Pratama Wijaya',
                'ketua_kelas' => 'XI-F1',
                'wakil_nama' => 'Nadia Putri Azzahra',
                'wakil_kelas' => 'X-E2',
                'visi' => 'Mewujudkan OSIS yang inklusif, kolaboratif, dan adaptif terhadap transformasi digital sekolah.',
                'misi' => "1. Mengoptimalkan portal aspirasi siswa berbasis digital.\n2. Mengadakan festival karya & inovasi antar kelas.\n3. Memperkuat program literasi dan kepedulian lingkungan.",
                'status' => 'aktif',
            ],
            [
                'nomor_urut' => 2,
                'kode_paslon' => 'PASLON-02',
                'ketua_nama' => 'Dimas Aditya Saputra',
                'ketua_kelas' => 'XI-F3',
                'wakil_nama' => 'Salsabila Khairunnisa',
                'wakil_kelas' => 'XI-F2',
                'visi' => 'Menjadikan OSIS sebagai wadah prestasi, karakter unggul, dan solidaritas siswa.',
                'misi' => "1. Menghidupkan kembali ekstrakurikuler berbasis minat bakat.\n2. Transparansi anggaran kegiatan OSIS secara berkala.\n3. Menggelar pekan olahraga dan seni tahunan.",
                'status' => 'aktif',
            ],
            [
                'nomor_urut' => 3,
                'kode_paslon' => 'PASLON-03',
                'ketua_nama' => 'Fajar Ramadhan Hakim',
                'ketua_kelas' => 'XI-F2',
                'wakil_nama' => 'Keisha Aurelia Putri',
                'wakil_kelas' => 'X-E1',
                'visi' => 'Membangun ekosistem sekolah yang kreatif, berintegritas, dan ramah bagi seluruh warga sekolah.',
                'misi' => "1. Program bank sampah dan sekolah bebas plastik.\n2. Pendampingan lomba akademik & non-akademik.\n3. Forum dialog terbuka rutin antara siswa dan pembina.",
                'status' => 'aktif',
            ],
        ];

        foreach ($candidates as $c) {
            Candidate::firstOrCreate(['kode_paslon' => $c['kode_paslon']], $c);
        }

        $votersData = [
            ['nis' => '202601001', 'nama' => 'Ahmad Fauzan', 'kelas' => 'X-E1', 'tps' => 'TPS-01', 'token' => 'OS1001', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202601002', 'nama' => 'Aulia Rahmawati', 'kelas' => 'X-E1', 'tps' => 'TPS-01', 'token' => 'OS1002', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202601003', 'nama' => 'Bagas Kurniawan', 'kelas' => 'X-E2', 'tps' => 'TPS-01', 'token' => 'OS1003', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202601004', 'nama' => 'Citra Dewi Lestari', 'kelas' => 'X-E2', 'tps' => 'TPS-01', 'token' => 'OS1004', 'has_voted' => false, 'status' => 'terverifikasi'],
            ['nis' => '202602001', 'nama' => 'Daffa Alamsyah', 'kelas' => 'XI-F1', 'tps' => 'TPS-02', 'token' => 'OS2001', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202602002', 'nama' => 'Elsa Maharani', 'kelas' => 'XI-F1', 'tps' => 'TPS-02', 'token' => 'OS2002', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202602003', 'nama' => 'Farhan Rizky', 'kelas' => 'XI-F2', 'tps' => 'TPS-02', 'token' => 'OS2003', 'has_voted' => false, 'status' => 'terverifikasi'],
            ['nis' => '202602004', 'nama' => 'Gita Permata Sari', 'kelas' => 'XI-F3', 'tps' => 'TPS-02', 'token' => 'OS2004', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202603001', 'nama' => 'Hendra Wijaya', 'kelas' => 'XII-A1', 'tps' => 'TPS-03', 'token' => 'OS3001', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202603002', 'nama' => 'Intan Nuraini', 'kelas' => 'XII-A1', 'tps' => 'TPS-03', 'token' => 'OS3002', 'has_voted' => false, 'status' => 'belum_aktivasi'],
            ['nis' => '202603003', 'nama' => 'Jihan Salsabila', 'kelas' => 'XII-A2', 'tps' => 'TPS-03', 'token' => 'OS3003', 'has_voted' => true, 'status' => 'terverifikasi'],
            ['nis' => '202603004', 'nama' => 'Kevin Mahendra', 'kelas' => 'XII-A2', 'tps' => 'TPS-03', 'token' => 'OS3004', 'has_voted' => false, 'status' => 'terverifikasi'],
        ];

        $allCandidates = Candidate::orderBy('nomor_urut')->get();

        foreach ($votersData as $idx => $v) {
            $tps = $tpsMap[$v['tps']] ?? null;
            $voter = Voter::firstOrCreate(
                ['nis' => $v['nis']],
                [
                    'nis' => $v['nis'],
                    'nama' => $v['nama'],
                    'kelas' => $v['kelas'],
                    'tps_id' => $tps?->id,
                    'token' => $v['token'],
                    'has_voted' => $v['has_voted'],
                    'voted_at' => $v['has_voted'] ? now()->subMinutes(rand(3, 120)) : null,
                    'receipt_code' => $v['has_voted'] ? 'BKT-' . strtoupper(Str::random(6)) : null,
                    'ip_address' => $v['has_voted'] ? '192.168.1.' . rand(10, 99) : null,
                    'browser' => $v['has_voted'] ? 'Google Chrome 131.0' : null,
                    'os' => $v['has_voted'] ? ($idx % 2 === 0 ? 'Android 14' : 'iOS') : null,
                    'device_type' => $v['has_voted'] ? 'Mobile' : null,
                    'user_agent' => $v['has_voted'] ? 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/131.0.0.0 Mobile Safari/537.36' : null,
                    'status' => $v['status'],
                ]
            );

            if ($voter->has_voted && !$voter->vote && $allCandidates->isNotEmpty()) {
                $candidate = $allCandidates[$idx % $allCandidates->count()];
                Vote::create([
                    'candidate_id' => $candidate->id,
                    'voter_id' => $voter->id,
                    'tps_id' => $tps?->id,
                ]);
            }
        }
    }
}
