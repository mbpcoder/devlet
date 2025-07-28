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
            'php5.6-curl',
            'php5.6-mbstring',
            'php5.6-xml',
            'php5.6-zip',
            'php5.6-mysql',

            'php7.4-cli',
            'php7.4-fpm',
            'php7.4-common',
            'php7.4-curl',
            'php7.4-mbstring',
            'php7.4-xml',
            'php7.4-zip',
            'php7.4-mysql',

            'php8.1-cli',
            'php8.1-fpm',
            'php8.1-common',
            'php8.1-curl',
            'php8.1-mbstring',
            'php8.1-xml',
            'php8.1-zip',
            'php8.1-mysql',

            'php8.2-cli',
            'php8.2-fpm',
            'php8.2-common',
            'php8.2-curl',
            'php8.2-mbstring',
            'php8.2-xml',
            'php8.2-zip',
            'php8.2-mysql',

            'php8.4-cli',
            'php8.4-fpm',
            'php8.4-common',
            'php8.4-curl',
            'php8.4-mbstring',
            'php8.4-xml',
            'php8.4-zip',
            'php8.4-mysql',
        ],

        'apache_modules' => [
            'ssl',
            'rewrite',
            'proxy_fcgi',
            'setenvif',
        ],
    ],
];
