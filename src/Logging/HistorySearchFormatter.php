<?php

namespace Workable\RequestLogging\Logging;

use Monolog\Formatter\LineFormatter;

class HistorySearchFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter(
                '[%datetime%] %message%' . "\n"
            ));
        }
    }
}