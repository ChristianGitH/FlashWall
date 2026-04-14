<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($image) {
            $image->last_status_update = now();
        });
    }

    protected $fillable = [
        'wall_id',
        'name',
        'webp_name',
        'thumb',
        'caption',
        'status',
        'visitor_token',
        'submitter_id',
        'submitter_name',
        'submitter_avatar',
        'priority',
        'parent_id',
        'pinned',
        'last_status_update',
    ];

    protected $hidden = [
        'wall_id',
        'visitor_token',
        'priority',
    ];

    // Relations : An image belong to a wall.
    public function wall(): BelongsTo
    {
        return $this->belongsTo(Wall::class);
    }

    public function parent()
    {
        return $this->belongsTo(Image::class, 'parent_id');
    }

    public function copies()
    {
        return $this->hasMany(Image::class, 'parent_id');
    }
    
    public function submitter()
    {
        return $this->belongsTo(Submitter::class);
    }


    // ACCESSOR to get full path with $image->original_full_path, $image->wepb_full_path and $image->thumb_full_path.
    public function getOriginalFullPathAttribute()
    {
        return 'walls_images/images_submitters/' . $this->name;
    }
    public function getWebpFullPathAttribute()
    {
        return 'walls_images/webp_images_submitters/' . $this->webp_name;
    }

    public function getThumbFullPathAttribute()
    {
        return 'walls_images/thumbs_submitters/' . $this->thumb;
    }
}
