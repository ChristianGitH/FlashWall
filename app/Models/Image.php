<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'wall_id',
        'name',
        'thumb',
        'caption',
        'status',
        'visitor_token',
        'submitter_id',
        'submitter_name',
        'priority',
        'permanent',
        'parent_id',
    ];

    protected $hidden = [
        'wall_id',
        'visitor_token',
        'priority',
        'permanent',
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
}
