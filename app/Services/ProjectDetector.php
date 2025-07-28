<?php

declare(strict_types=1);

namespace App\Services;

final readonly class ProjectDetector
{
    public function detect(string $projectPath): string
    {
        if (file_exists($projectPath . '/artisan')) {
            return 'laravel';
        }

        if (file_exists($projectPath . '/wp-config.php')) {
            return 'wordpress';
        }

        if (file_exists($projectPath . '/bin/console') && is_dir($projectPath . '/config')) {
            return 'symfony';
        }

        if (file_exists($projectPath . '/index.php') && is_dir($projectPath . '/system')) {
            return 'codeigniter';
        }

        return 'unknown';
    }

    public function getDocRoot(string $type, string $projectPath): string
    {
        return match ($type) {
            'laravel'     => $projectPath . '/public',
            'wordpress'   => $projectPath,
            'symfony'     => $projectPath . '/public',
            'codeigniter' => $projectPath,
            default       => $projectPath,
        };
    }
}
