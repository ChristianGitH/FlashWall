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
        'allow_captions',
        'moderation',
        'max_images_submitter',
        'ask_name_submitter',
        'ask_email_submitter',
        'require_name_submitter',
        'require_email_submitter',
        'submitter_name_on_wall',
        'caption_on_wall',
        'background_choice',
        'background_color',
        'background_image',
        'caption_max_width',
        'caption_position',
        'caption_font_size',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'duration',
        'transition',
        'caption_font_color',
        'caption_background_color',
        'caption_background_opacity',
        'caption_max_characters',
        'posting_page_text',
        'posting_page_text_visibility',
        'posting_page_buttons_color',
        'posting_page_buttons_font_color',
        'posting_page_logo',
        'posting_page_logo_visibility',
        'posting_page_background_color',
        'posting_page_background_image',
        'posting_page_background_choice'
    ];


    /**
     * Cast des attributs pour garantir une bonne comparaison avec isDirty(), (ADDED when adding wall settings)
     */
    protected $casts = [
        'allow_captions' => 'boolean',
        'moderation' => 'boolean',
        'ask_name_submitter' => 'boolean',
        'ask_email_submitter' => 'boolean',
        'require_name_submitter' => 'boolean',
        'require_email_submitter' => 'boolean',
        'submitter_name_on_wall' => 'boolean',
        'caption_on_wall' => 'boolean',
        'posting_page_logo_visibility' => 'boolean',
        'posting_page_text_visibility' => 'boolean',
        
        'background_choice' => 'integer',
        'caption_max_width' => 'integer',
        'caption_position' => 'integer',
        'caption_font_size' => 'integer',
        'margin_top' => 'integer',
        'margin_bottom' => 'integer',
        'margin_left' => 'integer',
        'margin_right' => 'integer',
        'duration' => 'integer',
        'caption_background_opacity' => 'integer',
        'caption_max_characters' => 'integer',
        'max_images_submitter' => 'integer',
        'posting_page_logo_visibility' => 'integer',
        'posting_page_background_choice' => 'integer',
    ];


        /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
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

    public function getRouteKeyName()
    {
        return 'slug';
    }
}

