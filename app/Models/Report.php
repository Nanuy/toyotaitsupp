<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use App\Models\ReportDetail;

class Report extends Model
{
    use HasFactory;

   protected $fillable = [
    'reporter_name',
    'division',
    'contact',
    'item_id',
    'location_id',
    'description',
    'status',
    'surat_jalan_date',
    'image',
];


protected $casts = [
    'created_at' => 'datetime',
    'surat_jalan_date' => 'date',
];



    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function itSupports()
    {
        return $this->belongsToMany(User::class, 'report_user', 'report_id', 'user_id');
    }

    // ✅ Relasi ke detail laporan
    public function details()
    {
        return $this->hasMany(ReportDetail::class);
    }
}
