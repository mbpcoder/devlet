<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'port',
        'version',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'port' => 'integer',
    ];
}
