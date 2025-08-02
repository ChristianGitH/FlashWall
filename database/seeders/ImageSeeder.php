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
                'name' => 'images/N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg',
                'thumb' => 'thumbs/N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg',
                'caption' => 'Image : '. $i . '. La super légende !',
                'visitor_token' => '1458-afgd',
            ]);
            $i++;
        }
    }
}