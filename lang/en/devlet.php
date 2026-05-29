<?php

return [

    'app_name' => 'DevLet',
    'tagline' => 'Local Development Environment Manager',

    'nav' => [
        'dashboard' => 'Dashboard',
        'hosts' => 'Hosts',
        'services' => 'Services',
        'php_versions' => 'PHP Versions',
        'certificates' => 'Certificates',
        'settings' => 'Settings',
    ],

    'hosts' => [
        'label' => 'Host',
        'plural' => 'Hosts',
        'fields' => [
            'name' => 'Name',
            'full_path' => 'Project Path',
            'php_version' => 'PHP Version',
            'domain' => 'Domain',
            'document_root' => 'Document Root',
            'web_server' => 'Web Server',
            'framework' => 'Framework',
            'active' => 'Active',
            'ssl_enabled' => 'SSL Enabled',
            'notes' => 'Notes',
        ],
        'actions' => [
            'enable_site' => 'Enable Site',
            'disable_site' => 'Disable Site',
            'regenerate_ssl' => 'Regenerate SSL',
            'open_in_browser' => 'Open in Browser',
        ],
    ],

    'services' => [
        'label' => 'Service',
        'plural' => 'Services',
        'fields' => [
            'name' => 'Name',
            'slug' => 'Slug',
            'type' => 'Type',
            'status' => 'Status',
            'port' => 'Port',
            'version' => 'Version',
            'active' => 'Active',
        ],
        'types' => [
            'web_server' => 'Web Server',
            'database' => 'Database',
            'cache' => 'Cache',
            'mail' => 'Mail',
            'other' => 'Other',
        ],
        'status' => [
            'running' => 'Running',
            'stopped' => 'Stopped',
            'unknown' => 'Unknown',
        ],
        'actions' => [
            'start' => 'Start',
            'stop' => 'Stop',
            'restart' => 'Restart',
            'reload' => 'Reload',
        ],
    ],

    'php_versions' => [
        'label' => 'PHP Version',
        'plural' => 'PHP Versions',
        'fields' => [
            'version' => 'Version',
            'is_default' => 'Default',
            'binary_path' => 'Binary Path',
            'fpm_socket' => 'FPM Socket',
            'installed' => 'Installed',
        ],
        'actions' => [
            'install' => 'Install',
            'uninstall' => 'Uninstall',
            'set_default' => 'Set as Default',
        ],
    ],

    'certificates' => [
        'label' => 'Certificate',
        'plural' => 'Certificates',
        'fields' => [
            'host' => 'Host',
            'domain' => 'Domain',
            'cert_path' => 'Certificate Path',
            'key_path' => 'Key Path',
            'expires_at' => 'Expires At',
        ],
        'actions' => [
            'regenerate' => 'Regenerate',
        ],
    ],

    'settings' => [
        'label' => 'Setting',
        'plural' => 'Settings',
        'fields' => [
            'key' => 'Key',
            'value' => 'Value',
            'group' => 'Group',
        ],
        'groups' => [
            'general' => 'General',
            'paths' => 'Paths',
            'defaults' => 'Defaults',
            'network' => 'Network',
        ],
    ],

    'frameworks' => [
        'laravel' => 'Laravel',
        'wordpress' => 'WordPress',
        'symfony' => 'Symfony',
        'codeigniter' => 'CodeIgniter',
        'unknown' => 'Unknown',
    ],

    'web_servers' => [
        'apache2' => 'Apache 2',
        'nginx' => 'Nginx',
    ],

    'widgets' => [
        'services_status' => [
            'title' => 'Services Status',
        ],
        'hosts_overview' => [
            'title' => 'Hosts Overview',
            'total' => 'Total Hosts',
            'active' => 'Active',
            'ssl' => 'SSL Enabled',
        ],
    ],
];
