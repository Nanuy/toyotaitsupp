<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    
    // Nonaktifkan timestamps karena tabel hanya memiliki id dan name
    public $timestamps = false;

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    
    // Method untuk menghitung jumlah laporan berdasarkan report_details
    public function getReportCountAttribute()
    {
        return DB::table('report_details')
            ->join('reports', 'report_details.report_id', '=', 'reports.id')
            ->where('report_details.item_id', $this->id)
            ->distinct('reports.id')
            ->count('reports.id');
    }
}
