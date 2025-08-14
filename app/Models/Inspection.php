<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'merek_tipe',
        'dampak_ditimbulkan',
        'tindakan_dilakukan',
        'rekomendasi_teknis',
        'spesifikasi_pengadaan',
        'status',
        'inspector_id',
        'inspection_date'
    ];

    protected $casts = [
        'inspection_date' => 'datetime'
    ];

    // Relasi ke Report
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Relasi ke User (inspector)
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
