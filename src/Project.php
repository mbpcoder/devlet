<?php
declare(strict_types=1);

namespace DevLet;

final class Project
{
    public readonly string $path;
    public readonly string $phpPath;
    public readonly string $domain;
    public readonly string $docRoot;

    public function __construct(string $path, string $phpPath, string $domain, ?string $docRoot = null)
    {
        $this->path = rtrim($path, '/');
        $this->phpPath = $phpPath;
        $this->domain = $domain;
        $this->docRoot = $docRoot ?? ($this->path . '/public');
    }
}
