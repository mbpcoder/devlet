<?php
declare(strict_types=1);

namespace DevLet;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

final class LoggerService
{
    private Logger $logger;

    public function __construct(string $logFilePath = 'devlet.log')
    {
        $this->logger = new Logger('DevLet');

        // Human-friendly output format for file & console
        $output = "[%datetime%] %level_name%: %message%\n";
        $formatter = new LineFormatter($output, "Y-m-d H:i:s", true, true);

        // File log
        $fileHandler = new StreamHandler($logFilePath, Logger::DEBUG);
        $fileHandler->setFormatter($formatter);
        $this->logger->pushHandler($fileHandler);

        // Console log (STDOUT)
        $consoleHandler = new StreamHandler('php://stdout', Logger::INFO);
        $consoleHandler->setFormatter($formatter);
        $this->logger->pushHandler($consoleHandler);
    }

    public function log(string $message, string $level = 'info'): void
    {
        $level = strtolower($level);
        if (!method_exists($this->logger, $level)) {
            $level = 'info';
        }
        $this->logger->$level($message);
    }
}
