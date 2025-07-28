<?php

declare(strict_types=1);

function isRunningInWSL(): bool
{
    if (is_readable('/proc/version')) {
        $version = file_get_contents('/proc/version');
        if (stripos($version, 'microsoft') !== false || stripos($version, 'wsl') !== false) {
            return true;
        }
    }

    $info = shell_exec('uname -r');
    if (stripos($info, 'microsoft') !== false || stripos($info, 'wsl') !== false) {
        return true;
    }

    return false;
}

function getWslIp(): ?string
{
    $output = shell_exec("ip addr show eth0 | grep 'inet ' | awk '{print \$2}' | cut -d/ -f1");
    $ip = trim($output);

    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
}
