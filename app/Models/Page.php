<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'template',
        'html',
        'content_blocks',
        'meta_title',
        'meta_description',
        'og_image',
        'canonical_url',
        'is_published',
    ];

    protected $casts = [
        'content_blocks' => 'array',
        'is_published' => 'boolean',
    ];
}
