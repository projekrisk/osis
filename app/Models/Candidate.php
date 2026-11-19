<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    protected $fillable = [
        'nomor_urut',
        'kode_paslon',
        'ketua_nama',
        'ketua_kelas',
        'wakil_nama',
        'wakil_kelas',
        'visi',
        'misi',
        'foto_path',
        'status',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
