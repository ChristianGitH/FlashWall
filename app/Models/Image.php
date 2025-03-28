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
        'approved',
        'archived',
    ];

    protected $hidden = [
        'wall_id',
        'name',
        'thumb',
    ];

    // Relations : An image belong to one user.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relations : A wall belong to one user.
    public function wall(): BelongsTo
    {
        return $this->belongsTo(Wall::class);
    }
}
