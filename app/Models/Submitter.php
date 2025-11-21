<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submitter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'avatar', 'token'];

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
