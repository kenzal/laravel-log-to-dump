<?php

namespace Kenzal\LogToDump;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class DumpHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        dump($record->formatted ?? $record->message);
    }
}
