<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use App\Models\ReportDetail;
use App\Models\Signature; // ⚠️ TAMBAHKAN INI - import model Signature

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
        'report_code',     
        'report_pass',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'surat_jalan_date' => 'date',
    ];

    // ✅ Relasi ke Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // ✅ Relasi ke Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // ✅ Relasi many-to-many dengan User (IT Support) melalui tabel report_user
    public function itSupports()
    {
        return $this->belongsToMany(User::class, 'report_user', 'report_id', 'user_id');
    }

    // ✅ Alias untuk konsistensi dengan view yang sudah ada
    public function users()
    {
        return $this->itSupports();
    }

    // ✅ Relasi ke detail laporan
    public function details()
    {
        return $this->hasMany(ReportDetail::class);
    }

    // ✅ Relasi ke signatures
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'report_id');
    }

    // ✅ Method helper untuk mendapatkan signature berdasarkan role
    public function signatureByRole($role)
    {
        return $this->signatures()->where('role', $role)->first();
    }

    // ✅ Method helper untuk mendapatkan tanda tangan user tertentu
    public function getSignatureForUser($userId)
    {
        return $this->signatures()->where('user_id', $userId)->first();
    }

    // ✅ Method helper khusus untuk signature reporter/pengguna
    public function reporterSignature()
    {
        return $this->signatureByRole('user');
    }

    // ✅ Method helper untuk signature IT Support
    public function itSupportSignatures()
    {
        return $this->signatures()->where('role', 'it_supp')->get();
    }

    // ✅ Method helper untuk cek apakah semua IT Support sudah tanda tangan
    public function allITSigned()
    {
        $totalIT = $this->itSupports()->count();
        $totalSigned = $this->signatures()->where('role', 'it_supp')->count();
        
        return $totalIT > 0 && $totalIT === $totalSigned;
    }

    // ✅ Method helper untuk cek progress tanda tangan
    public function getSignatureProgress()
    {
        $totalIT = $this->itSupports()->count();
        $totalSigned = $this->signatures()->where('role', 'it_supp')->count();
        
        return [
            'total' => $totalIT,
            'signed' => $totalSigned,
            'percentage' => $totalIT > 0 ? round(($totalSigned / $totalIT) * 100) : 0,
            'all_signed' => $totalIT > 0 && $totalIT === $totalSigned
        ];
    }

    

    // ✅ Method helper untuk cek apakah user tertentu sudah tanda tangan
    public function hasUserSigned($userId)
    {
        return $this->signatures()->where('user_id', $userId)->exists();
    }

    // ✅ Method helper untuk mendapatkan semua user yang sudah tanda tangan
    public function getSignedUsers()
    {
        return $this->signatures()
            ->with('user')
            ->where('role', 'it_supp')
            ->get()
            ->pluck('user')
            ->filter(); // Remove null values
    }

    // ✅ Method helper untuk mendapatkan user yang belum tanda tangan
    public function getUnsignedUsers()
    {
        $signedUserIds = $this->signatures()
            ->where('role', 'it_supp')
            ->pluck('user_id')
            ->toArray();

        return $this->itSupports()
            ->whereNotIn('users.id', $signedUserIds)
            ->get();
    }

    // ✅ Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // ✅ Scope untuk filter berdasarkan IT Support
    public function scopeByItSupport($query, $userId)
    {
        return $query->whereHas('itSupports', function($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    // ✅ Accessor untuk status badge
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'waiting':
                return '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu</span>';
            case 'accepted':
                return '<span class="badge bg-info text-white"><i class="fas fa-cog"></i> Dikerjakan</span>';
            case 'completed':
                return '<span class="badge bg-success"><i class="fas fa-check"></i> Selesai</span>';
            default:
                return '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>';
        }
    }

    // ✅ Accessor untuk formatted date
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    // ✅ Accessor untuk surat jalan date formatted
    public function getFormattedSuratJalanDateAttribute()
    {
        return $this->surat_jalan_date ? $this->surat_jalan_date->format('d/m/Y') : null;
    }
}