<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = [
        'report_id',
        'user_id',
        'role',
        'signature_path',
        'is_checked',
        'is_auto',
        'signed_at',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'is_auto' => 'boolean',
        'signed_at' => 'datetime',
    ];

    // Relasi ke Report
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Relasi ke User (optional, karena bisa null untuk public user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}