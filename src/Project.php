<?php
declare(strict_types=1);

namespace DevLet;

final class Project
{
    public readonly string $path;
    public readonly string $phpVersion;
    public readonly string $domain;
    public readonly string $docRoot;

    public function __construct(string $path, string $phpVersion, string $domain, ?string $docRoot = null)
    {
        $this->path = rtrim($path, '/');
        $this->phpVersion = preg_replace('/^(\d+\.\d+).*/', '$1', $phpVersion); // "8.3";
        $this->domain = $domain;
        $this->docRoot = $docRoot ?? ($this->path . '/public');
    }
}
