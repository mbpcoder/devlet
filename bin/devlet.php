<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DevLet\HostsManager;
use DevLet\SSLManager;
use DevLet\ApacheManager;
use DevLet\DevLetConfigurator;
use DevLet\ProjectDetector;
use DevLet\LoggerService;


$projectsPath = '/mnt/c/Users/mahdi.bagheri/Documents/Projects';

$hostsManager = new HostsManager();
$sslManager = new SSLManager();
$apacheManager = new ApacheManager();

$logger = new LoggerService(__DIR__ . '/../devlet.log');

$configurator = new DevLetConfigurator(
    $projectsPath,
    new HostsManager(),
    new SSLManager(),
    new ApacheManager(),
    $logger,
    new ProjectDetector(),

);
$configurator->run();
