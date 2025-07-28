<?php

declare(strict_types=1);

namespace App\Services;

final class HostsService
{
    private const string COMMENT_TAG = '#devlet';
    private const string LOCALHOST_IP = '127.0.0.1';

    public function __construct(
        private readonly string $hostsFile = '/etc/hosts',
        private readonly string $wslHostFile = '/mnt/c/Windows/System32/drivers/etc/hosts'
    )
    {
    }

    /**
     * @param string[] $domains List of base domains like ['abb.local', 'site.test']
     */
    public function syncEntries(array $domains): void
    {
        $this->syncFileEntries($this->hostsFile, $domains, ip: self::LOCALHOST_IP);

        if (isRunningInWSL()) {
            $wslIp = getWslIp();
            if ($wslIp) {
                $this->syncFileEntries($this->wslHostFile, $domains, ip: $wslIp);
            }
        }
    }

    /**
     * @param string[] $domains Flat list of base domains
     */
    private function syncFileEntries(string $path, array $domains, string $ip): void
    {
        if (!is_file($path) || !is_readable($path) || !is_writable($path)) {
            throw new \RuntimeException("Cannot access or modify hosts file: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
        $newLines = [];

        foreach ($lines as $line) {
            if (!str_contains($line, self::COMMENT_TAG)) {
                $newLines[] = $line;
            }
        }

        foreach ($domains as $domain) {
            $line = "$ip $domain www.$domain " . self::COMMENT_TAG;
            $newLines[] = $line;
        }

        file_put_contents($path, implode(PHP_EOL, $newLines) . PHP_EOL);
    }
}
