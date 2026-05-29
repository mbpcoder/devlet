<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhpVersion extends Model
{
    protected $table = 'php_versions';

    protected $fillable = [
        'version',
        'is_default',
        'binary_path',
        'fpm_socket',
        'installed',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'installed' => 'boolean',
    ];
}
