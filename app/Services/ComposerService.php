<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;

final class ComposerService
{
    /**
     * Attempt to extract PHP version constraint from composer.json.
     */
    public function detectPhpVersion(string $projectPath): string|null
    {
        $composerFile = $projectPath . DIRECTORY_SEPARATOR . 'composer.json';

        if (!File::exists($composerFile)) {
            return null;
        }

        $json = json_decode(File::get($composerFile), true);

        if (!is_array($json) || empty($json['require']['php'])) {
            return null;
        }

        $constraint = $json['require']['php'];

        // Extract first matching major.minor version from constraint (e.g., ^8.2 => 8.2)
        if (preg_match('/(\d+\.\d+)/', $constraint, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
