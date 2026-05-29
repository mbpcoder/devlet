<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Host extends Model
{
    protected $fillable = [
        'name',
        'full_path',
        'php_version',
        'domain',
        'document_root',
        'web_server',
        'framework',
        'active',
        'ssl_enabled',
        'notes',
    ];

    protected $casts = [
        'active' => 'boolean',
        'ssl_enabled' => 'boolean',
    ];

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
