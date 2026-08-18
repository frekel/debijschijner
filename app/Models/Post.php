<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'type',
        'slug',
        'name',
        'title',
        'text',
        'editor_mode',
        'image',
        'button_text',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'editor_mode' => 'string',
        'slug' => 'string',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

