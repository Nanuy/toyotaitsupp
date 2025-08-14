<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category'];
    
    // Nonaktifkan timestamps karena tabel hanya memiliki id dan name
    public $timestamps = false;

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
