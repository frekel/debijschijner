<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoHit extends Model
{
    protected $fillable = [
        'page_id',
        'page_slug',
        'page_title',
        'path',
        'full_url',
        'redirect_target',
        'method',
        'host',
        'ip_address',
        'user_agent',
        'referer',
        'accept_language',
        'query_params',
        'headers',
    ];

    protected $casts = [
        'query_params' => 'array',
        'headers' => 'array',
    ];
}