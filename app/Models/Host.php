<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $fillable = [
        'name',
        'full_path',
        'php_version',
        'domain',
        'document_root',
        'web_server'
    ];
}
