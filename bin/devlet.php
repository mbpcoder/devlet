<?php
declare(strict_types=1);


require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use DevLet\Commands\InitOSCommand;
use DevLet\Commands\VerifyOSCommand;
use DevLet\Commands\ConfigureWebServerCommand;

$application = new Application('DevLet CLI', '1.0.0');

$application->add(new InitOSCommand());
$application->add(new VerifyOSCommand());
$application->add(new ConfigureWebServerCommand());

$application->run();