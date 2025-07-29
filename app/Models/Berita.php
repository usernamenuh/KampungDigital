<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'excerpt',
        'kategori',
        'status',
        'tingkat_akses',
        'rt_id',
        'rw_id',
        'user_id',
        'gambar',
        'video',
        'link',
        'tags',
        'is_pinned',
        'views',
        'tanggal_publish',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'tanggal_publish' => 'datetime',
        'views' => 'integer',
    ];

    protected $dates = [
        'tanggal_publish',
        'created_at',
        'updated_at',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul);
                
                // Ensure slug is unique
                $originalSlug = $berita->slug;
                $counter = 1;
                
                while (static::where('slug', $berita->slug)->exists()) {
                    $berita->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            // Set user_id if not set
            if (empty($berita->user_id) && auth()->check()) {
                $berita->user_id = auth()->id();
            }
            
            // Set tanggal_publish if status is published
            if ($berita->status === 'published' && empty($berita->tanggal_publish)) {
                $berita->tanggal_publish = now();
            }
        });

        static::updating(function ($berita) {
            if ($berita->isDirty('judul')) {
                $slug = Str::slug($berita->judul);
                
                // Only update slug if it's different and unique
                if ($slug !== $berita->slug) {
                    $originalSlug = $slug;
                    $counter = 1;
                    
                    while (static::where('slug', $slug)->where('id', '!=', $berita->id)->exists()) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    $berita->slug = $slug;
                }
            }
            
            // Set tanggal_publish when status changes to published
            if ($berita->isDirty('status') && $berita->status === 'published' && empty($berita->tanggal_publish)) {
                $berita->tanggal_publish = now();
            }
        });
    }

    /**
     * Get the author of the berita
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created the berita (alias for author)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the RT associated with the berita
     */
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * Get the RW associated with the berita
     */
    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    /**
     * Scope for published berita
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where(function ($q) {
                        $q->whereNull('tanggal_publish')
                          ->orWhere('tanggal_publish', '<=', now());
                    });
    }

    /**
     * Scope for draft berita
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for berita by category
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope for pinned berita
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope for berita accessible by user
     */
    public function scopeForUser($query, User $user)
    {
        $penduduk = $user->penduduk;
        
        if (!$penduduk || !$penduduk->kk) {
            return $query->where('tingkat_akses', 'desa');
        }

        return $query->where(function ($q) use ($penduduk) {
            $q->where('tingkat_akses', 'desa')
              ->orWhere(function ($subQ) use ($penduduk) {
                  $subQ->where('tingkat_akses', 'rw')
                       ->where('rw_id', $penduduk->kk->rt->rw_id);
              })
              ->orWhere(function ($subQ) use ($penduduk) {
                  $subQ->where('tingkat_akses', 'rt')
                       ->where('rt_id', $penduduk->kk->rt_id);
              });
        });
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->gambar) {
            return Storage::disk('public')->url($this->gambar);
        }
        return null;
    }

    /**
     * Get the video URL
     */
    public function getVideoUrlAttribute()
    {
        if ($this->video) {
            return Storage::disk('public')->url($this->video);
        }
        return null;
    }

    /**
     * Get formatted tags
     */
    public function getFormattedTagsAttribute()
    {
        if (is_array($this->tags)) {
            return implode(', ', $this->tags);
        }
        return $this->tags;
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Check if berita can be edited by user
     */
    public function canBeEditedBy(User $user): bool
    {
        // Admin and Kades can edit all berita
        if (in_array($user->role, ['admin', 'kades'])) {
            return true;
        }

        // Users can only edit their own berita
        return $this->user_id === $user->id;
    }

    /**
     * Check if berita can be deleted by user
     */
    public function canBeDeletedBy(User $user): bool
    {
        // Admin can delete all berita
        if ($user->role === 'admin') {
            return true;
        }

        // Kades can delete berita in their desa
        if ($user->role === 'kades') {
            return true;
        }

        // Users can only delete their own berita
        return $this->user_id === $user->id;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        switch ($this->status) {
            case 'published':
                return 'bg-green-100 text-green-800';
            case 'draft':
                return 'bg-yellow-100 text-yellow-800';
            case 'archived':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get kategori badge color
     */
    public function getKategoriBadgeColorAttribute()
    {
        $colors = [
            'umum' => 'bg-blue-100 text-blue-800',
            'pengumuman' => 'bg-red-100 text-red-800',
            'kegiatan' => 'bg-green-100 text-green-800',
            'pembangunan' => 'bg-yellow-100 text-yellow-800',
            'kesehatan' => 'bg-pink-100 text-pink-800',
            'pendidikan' => 'bg-purple-100 text-purple-800',
            'ekonomi' => 'bg-indigo-100 text-indigo-800',
            'sosial' => 'bg-teal-100 text-teal-800',
            'lingkungan' => 'bg-emerald-100 text-emerald-800',
            'keamanan' => 'bg-orange-100 text-orange-800',
        ];

        return $colors[$this->kategori] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get tingkat akses label
     */
    public function getTingkatAksesLabelAttribute()
    {
        switch ($this->tingkat_akses) {
            case 'desa':
                return 'Seluruh Desa';
            case 'rw':
                return 'RW ' . ($this->rw->no_rw ?? '');
            case 'rt':
                return 'RT ' . ($this->rt->no_rt ?? '') . ' - RW ' . ($this->rt->rw->no_rw ?? '');
            default:
                return 'Tidak Diketahui';
        }
    }

    /**
     * Get category color class (method called by view)
     */
    public function getCategoryColorClass()
    {
        $colors = [
            'umum' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            'pengumuman' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            'kegiatan' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            'pembangunan' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
            'kesehatan' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/20 dark:text-pink-400',
            'pendidikan' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
            'ekonomi' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400',
            'sosial' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/20 dark:text-teal-400',
            'lingkungan' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
            'keamanan' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
        ];

        return $colors[$this->kategori] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }

    /**
     * Get status color class (method called by view)
     */
    public function getStatusColorClass()
    {
        switch ($this->status) {
            case 'published':
                return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            case 'draft':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            case 'archived':
                return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        }
    }

    /**
     * Get target audience text (method called by view)
     */
    public function getTargetAudienceText()
    {
        switch ($this->tingkat_akses) {
            case 'desa':
                return 'Seluruh Desa';
            case 'rw':
                return 'RW ' . ($this->rw ? $this->rw->no_rw : '-');
            case 'rt':
                $rtText = 'RT ' . ($this->rt ? $this->rt->no_rt : '-');
                $rwText = $this->rt && $this->rt->rw ? ' RW ' . $this->rt->rw->no_rw : '';
                return $rtText . $rwText;
            default:
                return 'Tidak Diketahui';
        }
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
