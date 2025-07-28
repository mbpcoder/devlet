<?php
declare(strict_types=1);

namespace App\Services;

final class SSLService
{
    public function generate(string $domain): array|false
    {
        $certPath = "/etc/ssl/certs/$domain.pem";
        $keyPath = "/etc/ssl/private/$domain-key.pem";

        if (file_exists($certPath) && file_exists($keyPath)) {
            echo "SSL cert already exists for $domain\n";
            return [$certPath, $keyPath];
        }

        exec('command -v mkcert', $output, $returnVar);
        if ($returnVar !== 0) {
            echo "mkcert not installed. Please install mkcert.\n";
            return false;
        }

        if (!is_dir(dirname($certPath))) mkdir(dirname($certPath), 0755, true);
        if (!is_dir(dirname($keyPath))) mkdir(dirname($keyPath), 0700, true);

        $tmpDir = sys_get_temp_dir();
        $tmpCert = "$tmpDir/$domain.pem";
        $tmpKey = "$tmpDir/$domain-key.pem";

        $cmd = escapeshellcmd("mkcert -cert-file $tmpCert -key-file $tmpKey $domain www.$domain");

        exec($cmd, $out, $ret);

        if ($ret !== 0) {
            echo "mkcert command failed for $domain\n";
            return false;
        }

        if (!rename($tmpCert, $certPath) || !rename($tmpKey, $keyPath)) {
            echo "Failed to move SSL cert/key for $domain\n";
            return false;
        }

        chmod($certPath, 0644);
        chmod($keyPath, 0600);

        echo "Generated SSL cert/key for $domain\n";

        return [$certPath, $keyPath];
    }
}
