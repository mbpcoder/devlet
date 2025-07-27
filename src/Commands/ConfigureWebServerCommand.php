<?php

namespace DevLet\Commands;

use DevLet\HostsManager;
use DevLet\SSLManager;
use DevLet\ApacheManager;
use DevLet\DevLetConfigurator;
use DevLet\ProjectDetector;
use DevLet\LoggerService;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'configure',
    description: 'Configure the web server and projects hosts.'
)]
final class ConfigureWebServerCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectsPath = '/mnt/c/Users/mahdi.bagheri/Documents/Projects';

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

        $output->writeln('<info>Web server configuration completed.</info>');

        return Command::SUCCESS;
    }
}
