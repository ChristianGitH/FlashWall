<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wall extends Model
{

    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'user_id',
        'captions',
        'moderation',
        'max_images_user',
        'background_choice',
        'background_color',
        'background_image',
        'caption_max_width',
        'caption_position',
        'vertical_borders_width',
        'horizontal_borders_width',
        'duration'
    ];


    /**
     * Cast des attributs pour garantir une bonne comparaison avec isDirty(), (ADDED when adding wall settings)
     */
    protected $casts = [
        'captions' => 'boolean',
        'moderation' => 'boolean',
    ];


        /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
    ];

    // Relations : One wall belongs to one user.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relations : One wall has many images.
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}

