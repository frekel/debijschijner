<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplySubmission extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'trajectory',
        'message',
        'ip_address',
        'user_agent',
    ];
}
