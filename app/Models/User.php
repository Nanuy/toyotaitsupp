<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi secara massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'jabatan',
        'departemen',
        'role',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi otomatis tipe data kolom.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function assignedReports()
    {
    return $this->belongsToMany(Report::class, 'report_user');
    }

}
