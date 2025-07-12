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
            'captions' => true,
            'moderation' => true,
            'max_images_user' => '2',
            'background_choice' => '0',
            'background_color' => '#f7a6d5',
            'background_image' => '0yNxkgcnf0vZDabiELyJK8HtvHniN9rmFCklYWTQ.jpg',
            'caption_max_width' => '60',
            'caption_position' => '1',
            'vertical_borders_width' => '25',
            'horizontal_borders_width' => '10',
            'duration' => '3'
        ]);
    }
}