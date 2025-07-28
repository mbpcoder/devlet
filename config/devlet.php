<?php

return [

    'vhost_roots' => explode(',', env('DEVLET_VHOST_ROOTS')),

    'vhost_prefix' => 'devlet-auto-',


    'dependencies' => [
        'base_packages' => [
            'apache2',
            'apache2',
            'git',
            'mkcert',

            'php5.6-cli',
            'php5.6-fpm',
            'php5.6-common',

            'php7.4-cli',
            'php7.4-fpm',
            'php7.4-common',

            'php8.1-cli',
            'php8.1-fpm',
            'php8.1-common',

            'php8.2-cli',
            'php8.2-fpm',
            'php8.2-common',

            'php8.4-cli',
            'php8.4-fpm',
            'php8.4-common',
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
    ],

];
