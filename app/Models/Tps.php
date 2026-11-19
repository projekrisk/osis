<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tps extends Model
{
    protected $table = 'tps';

    protected $fillable = [
        'kode_tps',
        'nama_tps',
        'lokasi',
        'status',
    ];

    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class, 'tps_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'tps_id');
    }
}
