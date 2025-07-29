<?php

namespace App\Services;

class WebServerService
{

    public function getVhostsDir(): string
    {
        // give this base on the config
        // that witch web server we are going to use
        return '/etc/apache2/sites-available';
    }

}
