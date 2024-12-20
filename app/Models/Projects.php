<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;
    protected $table = 'projects';

    protected $fillable = [
        'title',
        'slug',
        'short_desc',
        'content',
        'construction_type',
        'sector',
        'location',
        'image',
        'status',
    ];
}
