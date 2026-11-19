<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ElectionSetting extends Model
{
    protected $fillable = [
        'nama_sekolah',
        'nama_kegiatan',
        'tahun_ajaran',
        'status_pemilihan',
        'jadwal_mulai',
        'jadwal_selesai',
        'logo_path',
        'require_token',
        'allow_abstain',
    ];

    protected $casts = [
        'require_token' => 'boolean',
        'allow_abstain' => 'boolean',
    ];

    public static function current(): self
    {
        return self::firstOrCreate([], [
            'nama_sekolah' => 'SMA Negeri 1 Nusantara',
            'nama_kegiatan' => 'Pemilihan Ketua & Wakil Ketua OSIS',
            'tahun_ajaran' => '2026/2027',
            'status_pemilihan' => 'berlangsung',
            'jadwal_mulai' => now()->subHour()->format('Y-m-d H:i'),
            'jadwal_selesai' => now()->addHours(8)->format('Y-m-d H:i'),
            'require_token' => true,
            'allow_abstain' => false,
        ]);
    }

    public function votingState(): string
    {
        if ($this->status_pemilihan !== 'berlangsung') {
            return $this->status_pemilihan;
        }

        try {
            $now = now();
            $start = Carbon::parse($this->jadwal_mulai);
            $end = Carbon::parse($this->jadwal_selesai);

            if ($now->lt($start)) {
                return 'persiapan';
            }

            if ($now->gt($end)) {
                return 'selesai';
            }
        } catch (\Throwable) {
            return $this->status_pemilihan;
        }

        return 'berlangsung';
    }

    public function isVotingOpen(): bool
    {
        return $this->votingState() === 'berlangsung';
    }
}
