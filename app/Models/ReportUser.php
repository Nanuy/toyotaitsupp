<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    protected $table = 'report_user'; // nama tabel

    protected $fillable = ['report_id', 'user_id']; // kolom yang bisa diisi

    public $timestamps = false; // kalau tabel tidak punya created_at dan updated_at
}
