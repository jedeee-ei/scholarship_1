<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'priority',
        'is_published',
        'publish_date',
        'expiry_date',
        'created_by',
        'updated_by',
        'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];



    /**
     * Scope to get only published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function($q) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            });
    }
}
