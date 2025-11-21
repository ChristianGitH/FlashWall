<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Image;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        $i = 0;
        while ($i < 30) {
            Image::create([
                'wall_id' => 1,
                'parent_id' => null,
                'name' => 'N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg',
                'webp_name' => 'N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg',
                'thumb' => 'N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg',
                'caption' => 'Image : '. $i,
                'visitor_token' => '1458-afgd',
                'submitter_name' => 'John '. $i,
                'submitter_avatar' => '😊',
                'permanent' => true,
            ]);
            $i++;
        }
    }
}