<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SSLGenerationException;
use Illuminate\Support\Facades\Process;

final class SSLService
{
    public function generate(string $domain): array
    {
        $certPath = "/etc/ssl/certs/{$domain}.pem";
        $keyPath  = "/etc/ssl/private/{$domain}-key.pem";

        if (file_exists($certPath) && file_exists($keyPath)) {
            $this->output("✅ SSL certificate already exists for {$domain}");
            return [$certPath, $keyPath];
        }

        $this->ensureMkcertInstalled();

        if ($this->isWSL()) {
            $this->syncRootCAFromWindows();
        }

        $this->prepareDirectory($certPath, 0755);
        $this->prepareDirectory($keyPath, 0700);

        $tmpDir   = sys_get_temp_dir();
        $tmpCert  = "{$tmpDir}/{$domain}.pem";
        $tmpKey   = "{$tmpDir}/{$domain}-key.pem";

        $command = "mkcert -cert-file {$tmpCert} -key-file {$tmpKey} {$domain} www.{$domain}";
        $result  = Process::run($command);

        if (!$result->successful()) {
            throw new SSLGenerationException("❌ mkcert failed:\n" . $result->errorOutput());
        }

        if (!rename($tmpCert, $certPath) || !rename($tmpKey, $keyPath)) {
            throw new SSLGenerationException("❌ Failed to move certs to final destination.");
        }

        chmod($certPath, 0644);
        chmod($keyPath, 0600);

        $this->output("✅ SSL certificate generated for {$domain}");

        return [$certPath, $keyPath];
    }

    private function ensureMkcertInstalled(): void
    {
        if (!Process::run('command -v mkcert')->successful()) {
            throw new SSLGenerationException("❌ mkcert is not installed. Please install it first.");
        }
    }

    private function isWSL(): bool
    {
        return str_contains(file_get_contents('/proc/version'), 'Microsoft');
    }

    private function syncRootCAFromWindows(): void
    {
        $linuxCAPath = getenv('HOME') . '/.local/share/mkcert/rootCA.pem';
        if (file_exists($linuxCAPath)) {
            return;
        }

        $windowsUser = $this->detectWindowsUser();
        $windowsCAPath = "/mnt/c/Users/{$windowsUser}/AppData/Local/mkcert";

        if (!file_exists("{$windowsCAPath}/rootCA.pem")) {
            $this->output("⚠️ Could not find root CA in Windows. Please run `mkcert -install` in Windows first.");
            return;
        }

        @mkdir(dirname($linuxCAPath), 0700, true);

        copy("{$windowsCAPath}/rootCA.pem", $linuxCAPath);
        copy("{$windowsCAPath}/rootCA-key.pem", str_replace('rootCA.pem', 'rootCA-key.pem', $linuxCAPath));

        $this->output("📥 Copied root CA from Windows to WSL.");
    }

    private function detectWindowsUser(): string
    {
        $users = scandir('/mnt/c/Users');
        foreach ($users as $user) {
            if ($user === '.' || $user === '..') continue;
            if (is_dir("/mnt/c/Users/{$user}/AppData/Local/mkcert")) {
                return $user;
            }
        }

        throw new SSLGenerationException("❌ Could not detect Windows user with mkcert installed.");
    }

    private function prepareDirectory(string $filePath, int $mode): void
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, $mode, true);
        }
    }

    private function output(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
