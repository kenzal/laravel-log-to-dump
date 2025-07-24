<?php

namespace Kenzal\LogToDump;

use Illuminate\Contracts\Container\Container;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Monolog\Logger;

class ServiceProvider extends BaseServiceProvider
{

    public function register(): void
    {
        $this->app->extend('log', function (LogManager $logManager) {
            $logManager->extend('dump', function (Container $app, array $config) {
                $handler = new DumpHandler();

                // Apply the formatter if specified in the config
                if (!empty($config['formatter'])) {
                    $formatterClass = $config['formatter'];
                    $formatter      = is_string($formatterClass) ? $app->make($formatterClass) : $formatterClass;
                    $handler->setFormatter($formatter);
                }

                // Set the log level if specified
                if (!empty($config['level'])) {
                    $handler->setLevel(Logger::toMonologLevel($config['level']));
                }

                $logger = new Logger('dump', [$handler]);

                // Add processors if specified
                if (!empty($config['processors'])) {
                    foreach ($config['processors'] as $processorClass) {
                        $processor = is_string($processorClass) ? $app->make($processorClass) : $processorClass;
                        $logger->pushProcessor($processor);
                    }
                }

                return $logger;
            });
            return $logManager;
        });
    }

    public function boot(): void
    {
        $this->mergeLoggingConfig();
    }

    public function getConfigPath(): string
    {
        return __DIR__ . '/config/logging.php';
    }

    protected function mergeLoggingConfig(): void
    {
        $customConfig = require $this->getConfigPath();
        $dumpChannel  = $customConfig['channels']['dump'] ?? null;

        if ($dumpChannel && !array_key_exists('dump', config('logging.channels', []))) {
            config(['logging.channels.dump' => $dumpChannel]);
        }
    }
}
