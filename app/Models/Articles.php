<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    use HasFactory;

    protected $table = 'articals';

    protected $fillable = [
        'title',
        'slug',
        'auther',
        'content',
        'image',
        'status'
    ];
}
