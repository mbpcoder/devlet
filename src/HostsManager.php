<?php
declare(strict_types=1);

namespace DevLet;

final class HostsManager
{
    private string $hostsFile;

    public function __construct(string $hostsFile = '/etc/hosts')
    {
        $this->hostsFile = $hostsFile;
    }

    public function syncEntries(array $currentDomains): void
    {
        $lines = file($this->hostsFile, FILE_IGNORE_NEW_LINES);
        $existingDevletEntries = [];
        $newLines = [];

        foreach ($lines as $line) {
            if (str_contains($line, '#devlet')) {
                preg_match('/^\s*127\.0\.0\.1\s+([^\s]+)\s+#devlet/', $line, $matches);
                if (!empty($matches[1])) {
                    $existingDevletEntries[$matches[1]] = $line;
                }
                // Skip old entry for now
                continue;
            }
            $newLines[] = $line; // Preserve all non-devlet lines
        }

        // Determine what to keep
        foreach ($currentDomains as $domain) {
            $entry = "127.0.0.1 $domain #devlet";
            unset($existingDevletEntries[$domain]);
            $newLines[] = $entry;
        }

        // Any remaining in $existingDevletEntries are stale — skipped intentionally (removed)

        file_put_contents($this->hostsFile, implode(PHP_EOL, $newLines) . PHP_EOL);
    }
}
