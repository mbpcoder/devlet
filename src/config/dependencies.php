<?php

return [
    'base_packages' => [
        'apache2',
        'openssl',
        'curl',
        'git',
        'unzip',
        'composer',
        'mkcert',
    ],

    'php_versions' => [
        '5.6',
        '7.4',
        '8.0',
        '8.1',
        '8.2',
        '8.3',
        '8.4',
    ],

    'php_packages' => [
        // base PHP package pattern, %s replaced with version
        'cli' => 'php%s-cli',
        'fpm' => 'php%s-fpm',
        'common' => 'php%s-common',
    ],

    'php_extensions' => [
        'curl',
        'mbstring',
        'xml',
        'zip',
        'mysql',
        'pdo',
        'cli',
        'fpm',
        'common',
        'json',
    ],

    'apache_modules' => [
        'ssl',
        'rewrite',
        'proxy_fcgi',
        'setenvif',
    ],
];
