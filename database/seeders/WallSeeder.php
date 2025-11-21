<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Wall;

class WallSeeder extends Seeder
{
    public function run(): void
    {
        Wall::create([
            'user_id' => 1,
            'name' => 'Mur 1',
            'slug' => 'mur1',
            'description' => 'Description du mur 1',
            'allow_captions' => true,
            'moderation' => true,
            'max_images_submitter' => '2',
            'capture_mode' => '0',
            'ask_name_submitter' => true,
            'ask_email_submitter' => true,
            'require_name_submitter' => false,
            'require_email_submitter' => false,
            'require_avatar_submitter' => false,
            'submitter_name_on_wall' => false,
            'caption_on_wall' => false,
            'background_choice' => '0',
            'background_color' => '#f7a6d5',
            'background_image' => 'default_background.jpg',
            'caption_max_width' => '60',
            'caption_position' => '1',
            'caption_font' => '',
            'caption_font_size' => '16',
            'margin_top' => '20',
            'margin_bottom' => '10',
            'margin_left' => '10',
            'margin_right' => '10',
            'duration' => '3',
            'transition' => 'fade',
            'caption_font_color' => '#ffffff',
            'caption_background_color' => '#ab58f6',
            'caption_background_opacity' => '30',
            'caption_max_characters' => '255',
            'posting_page_text' => 'Post an image',
            'posting_page_text_visibility' => true,
            'posting_page_font' => '',
            'posting_page_buttons_color' => '#4a00ff',
            'posting_page_buttons_font_color' => '#d1dbff',
            'posting_page_logo' => 'posting_page_default_logo.png',
            'posting_page_logo_visibility' => '0',
            'posting_page_background_color' => '#f8f8f8',
            'posting_page_background_image' => 'posting_page_default_background.png',
            'posting_page_background_choice' => '0'

        ]);
    }
}