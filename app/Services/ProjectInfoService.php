<?php

declare(strict_types=1);

namespace App\Services;

final readonly class ProjectInfoService
{
    public function detectFramework(string $projectPath): string
    {
        return match (true) {
            file_exists($projectPath . '/artisan') => 'laravel',
            file_exists($projectPath . '/wp-config.php') => 'wordpress',
            file_exists($projectPath . '/bin/console') && is_dir($projectPath . '/config') => 'symfony',
            file_exists($projectPath . '/index.php') && is_dir($projectPath . '/system') => 'codeigniter',
            default => 'unknown',
        };
    }

    public function resolveDocumentRoot(string $framework, string $projectPath, ?string $publicPath = null): string
    {
        $defaultPublicPath = match (strtolower($framework)) {
            'laravel', 'symfony' => 'public',
            'wordpress', 'codeigniter' => '',
            default => '',
        };

        $resolvedPath = $publicPath ?? $defaultPublicPath;

        return rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($resolvedPath, DIRECTORY_SEPARATOR);
    }
}
