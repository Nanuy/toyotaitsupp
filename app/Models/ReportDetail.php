<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Report;
use App\Models\Item;

class ReportDetail extends Model
{
    use HasFactory;

    protected $table = 'report_details';

    protected $fillable = [
        'report_id',
        'item_id',
        'tindakan',
        'uraian_masalah',
    ];

    // Relasi ke laporan utama
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Relasi ke item rusak (mouse, printer, dll)
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    // Report.php
public function details()
{
    return $this->hasMany(\App\Models\ReportDetail::class);
}

}
