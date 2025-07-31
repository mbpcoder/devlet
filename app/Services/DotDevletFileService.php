<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

final class DotDevletFileService
{
    public function parse(string $projectPath): array
    {
        $default = [
            'php' => null,
            'domain' => null,
            'public_path' => null,
        ];

        $file = $projectPath . DIRECTORY_SEPARATOR . '.devlet';

        if (!file_exists($file)) {
            return $default;
        }

        $ini = parse_ini_file($file, false, INI_SCANNER_TYPED);

        $php = $ini['php'] ?? null;
        $domain = $ini['domain'] ?? null;
        $publicPath = $ini['public_path'] ?? null;

        return [
            'php' => is_string($php) ? trim($php) : null,
            'domain' => is_string($domain) ? $this->normalizeDomain($domain) : null,
            'public_path' => is_string($publicPath) ? trim($publicPath) : null,
        ];
    }

    private function normalizeDomain(string $domain): string
    {
        return Str::of($domain)->trim()->lower()->toString();
    }
}
