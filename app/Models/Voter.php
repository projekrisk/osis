<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Voter extends Model
{
    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'tps_id',
        'token',
        'has_voted',
        'voted_at',
        'receipt_code',
        'ip_address',
        'browser',
        'os',
        'device_type',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'has_voted' => 'boolean',
        'voted_at' => 'datetime',
    ];

    public function tps(): BelongsTo
    {
        return $this->belongsTo(Tps::class, 'tps_id');
    }

    public function vote(): HasOne
    {
        return $this->hasOne(Vote::class);
    }
}
