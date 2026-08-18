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
        'form_title',
        'promo_redirect_url',
        'content_blocks',
        'meta_title',
        'meta_description',
        'og_image',
        'canonical_url',
        'sort_order',
        'is_published',
        'show_in_menu',
    ];

    protected $casts = [
        'content_blocks' => 'array',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'show_in_menu' => 'boolean',
    ];
}
