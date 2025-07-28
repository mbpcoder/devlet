<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SSLGenerationException;
use Illuminate\Support\Facades\Process;

final class SSLService
{

    public function generate(string $domain): array|false
    {
        $certPath = "/etc/ssl/certs/$domain.pem";
        $keyPath = "/etc/ssl/private/$domain-key.pem";

        if (file_exists($certPath) && file_exists($keyPath)) {
            return [$certPath, $keyPath];
        }

        // Check if mkcert is installed
        if (!Process::run('command -v mkcert')->successful()) {
            throw new SSLGenerationException("mkcert is not installed.");
        }


        if (!is_dir(dirname($certPath))) {
            mkdir(dirname($certPath), 0755, true);
        }

        if (!is_dir(dirname($keyPath))) {
            mkdir(dirname($keyPath), 0700, true);
        }

        $tmpDir = sys_get_temp_dir();
        $tmpCert = "$tmpDir/$domain.pem";
        $tmpKey = "$tmpDir/$domain-key.pem";

        $cmd = "mkcert -cert-file $tmpCert -key-file $tmpKey $domain www.$domain";

        $result = Process::run($cmd);

        if (!$result->successful()) {
            throw new SSLGenerationException("mkcert command failed: " . $result->errorOutput());
        }

        if (!rename($tmpCert, $certPath) || !rename($tmpKey, $keyPath)) {
            throw new SSLGenerationException("Failed to move generated cert/key to final location.");
        }

        chmod($certPath, 0644);
        chmod($keyPath, 0600);

        return [$certPath, $keyPath];
    }

    public function generate2(string $domain): array|false
    {
        $certPath = "/etc/ssl/certs/$domain.pem";
        $keyPath = "/etc/ssl/private/$domain-key.pem";

        if (file_exists($certPath) && file_exists($keyPath)) {
            echo "✅ SSL cert already exists for $domain\n";
            return [$certPath, $keyPath];
        }

        // Check if mkcert is available
        $check = Process::run('command -v mkcert');

        if (!$check->successful()) {
            echo "❌ mkcert not installed. Please install mkcert.\n";
            return false;
        }

        if (!is_dir(dirname($certPath))) {
            mkdir(dirname($certPath), 0755, true);
        }

        if (!is_dir(dirname($keyPath))) {
            mkdir(dirname($keyPath), 0700, true);
        }

        $tmpDir = sys_get_temp_dir();
        $tmpCert = "$tmpDir/$domain.pem";
        $tmpKey = "$tmpDir/$domain-key.pem";

        $cmd = "mkcert -cert-file $tmpCert -key-file $tmpKey $domain www.$domain";

        $result = Process::run($cmd);

        if (!$result->successful()) {
            echo "❌ mkcert command failed for $domain\n";
            echo $result->errorOutput();
            return false;
        }

        if (!rename($tmpCert, $certPath) || !rename($tmpKey, $keyPath)) {
            echo "❌ Failed to move SSL cert/key for $domain\n";
            return false;
        }

        chmod($certPath, 0644);
        chmod($keyPath, 0600);

        echo "✅ Generated SSL cert/key for $domain\n";

        return [$certPath, $keyPath];
    }
}
