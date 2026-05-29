<?php

return [

    'app_name' => 'دولت',
    'tagline' => 'مدیریت محیط توسعه محلی',

    'nav' => [
        'dashboard' => 'داشبورد',
        'hosts' => 'هاست‌ها',
        'services' => 'سرویس‌ها',
        'php_versions' => 'نسخه‌های PHP',
        'certificates' => 'گواهینامه‌ها',
        'settings' => 'تنظیمات',
    ],

    'hosts' => [
        'label' => 'هاست',
        'plural' => 'هاست‌ها',
        'fields' => [
            'name' => 'نام',
            'full_path' => 'مسیر پروژه',
            'php_version' => 'نسخه PHP',
            'domain' => 'دامنه',
            'document_root' => 'مسیر ریشه',
            'web_server' => 'وب سرور',
            'framework' => 'فریمورک',
            'active' => 'فعال',
            'ssl_enabled' => 'SSL فعال',
            'notes' => 'یادداشت',
        ],
        'actions' => [
            'enable_site' => 'فعال‌سازی سایت',
            'disable_site' => 'غیرفعال‌سازی سایت',
            'regenerate_ssl' => 'تجدید SSL',
            'open_in_browser' => 'باز کردن در مرورگر',
        ],
    ],

    'services' => [
        'label' => 'سرویس',
        'plural' => 'سرویس‌ها',
        'fields' => [
            'name' => 'نام',
            'slug' => 'شناسه',
            'type' => 'نوع',
            'status' => 'وضعیت',
            'port' => 'پورت',
            'version' => 'نسخه',
            'active' => 'فعال',
        ],
        'types' => [
            'web_server' => 'وب سرور',
            'database' => 'پایگاه داده',
            'cache' => 'کش',
            'mail' => 'ایمیل',
            'other' => 'سایر',
        ],
        'status' => [
            'running' => 'در حال اجرا',
            'stopped' => 'متوقف',
            'unknown' => 'نامشخص',
        ],
        'actions' => [
            'start' => 'شروع',
            'stop' => 'توقف',
            'restart' => 'راه‌اندازی مجدد',
            'reload' => 'بارگذاری مجدد',
        ],
    ],

    'php_versions' => [
        'label' => 'نسخه PHP',
        'plural' => 'نسخه‌های PHP',
        'fields' => [
            'version' => 'نسخه',
            'is_default' => 'پیش‌فرض',
            'binary_path' => 'مسیر باینری',
            'fpm_socket' => 'سوکت FPM',
            'installed' => 'نصب شده',
        ],
        'actions' => [
            'install' => 'نصب',
            'uninstall' => 'حذف',
            'set_default' => 'تنظیم به عنوان پیش‌فرض',
        ],
    ],

    'certificates' => [
        'label' => 'گواهینامه',
        'plural' => 'گواهینامه‌ها',
        'fields' => [
            'host' => 'هاست',
            'domain' => 'دامنه',
            'cert_path' => 'مسیر گواهینامه',
            'key_path' => 'مسیر کلید',
            'expires_at' => 'تاریخ انقضا',
        ],
        'actions' => [
            'regenerate' => 'تجدید',
        ],
    ],

    'settings' => [
        'label' => 'تنظیم',
        'plural' => 'تنظیمات',
        'fields' => [
            'key' => 'کلید',
            'value' => 'مقدار',
            'group' => 'گروه',
        ],
        'groups' => [
            'general' => 'عمومی',
            'paths' => 'مسیرها',
            'defaults' => 'پیش‌فرض‌ها',
            'network' => 'شبکه',
        ],
    ],

    'frameworks' => [
        'laravel' => 'لاراول',
        'wordpress' => 'وردپرس',
        'symfony' => 'سیمفونی',
        'codeigniter' => 'کدایگنایتر',
        'unknown' => 'نامشخص',
    ],

    'web_servers' => [
        'apache2' => 'آپاچی ۲',
        'nginx' => 'انجینکس',
    ],

    'widgets' => [
        'services_status' => [
            'title' => 'وضعیت سرویس‌ها',
        ],
        'hosts_overview' => [
            'title' => 'خلاصه هاست‌ها',
            'total' => 'مجموع هاست‌ها',
            'active' => 'فعال',
            'ssl' => 'SSL فعال',
        ],
    ],
];
