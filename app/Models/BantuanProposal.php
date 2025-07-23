<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BantuanProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'rw_id',
        'submitted_by',
        'judul_proposal',
        'deskripsi',
        'jumlah_bantuan',
        'file_proposal',
        'status',
        'reviewed_by',
        'reviewed_at',
        'catatan_review',
        'jumlah_disetujui',
        'tanggal_pencairan',
    ];

    protected $casts = [
        'jumlah_bantuan' => 'integer',
        'jumlah_disetujui' => 'integer',
        'reviewed_at' => 'datetime',
        'tanggal_pencairan' => 'datetime',
    ];

    // Relationships
    public function rw()
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedJumlahBantuanAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_bantuan, 0, ',', '.');
    }

    public function getFormattedJumlahDisetujuiAttribute()
    {
        return $this->jumlah_disetujui ? 'Rp ' . number_format($this->jumlah_disetujui, 0, ',', '.') : null;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getReviewedAtFormattedAttribute()
    {
        return $this->reviewed_at ? $this->reviewed_at->format('d/m/Y H:i') : null;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForRw($query, $rwId)
    {
        return $query->where('rw_id', $rwId);
    }
}
