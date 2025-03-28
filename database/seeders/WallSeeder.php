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
        ]);
    }
}