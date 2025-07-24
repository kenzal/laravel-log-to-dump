<?php

use Kenzal\LogToDump\DumpHandler;
use Kenzal\LogToDump\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\Processor\WebProcessor;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Depends;

class ServiceProviderTest extends TestCase
{

    public function testPackageAddsDumpChannel(): array
    {
        // Ensure that the dump channel is not present before booting the package
        $this->assertNotContains(needle  : 'dump',
                                 haystack: array_keys(config('logging.channels', [])),
                                 message : 'Dump channel should not exist before package boot.');

        // Boot the package
        $this->app->register(ServiceProvider::class);

        // Check if the dump channel is now present
        $this->assertContains(needle  : 'dump',
                              haystack: array_keys(config('logging.channels', [])),
                              message : 'Dump channel should be added by the package.');

        return config('logging.channels.dump');
    }

    #[Depends('testPackageAddsDumpChannel')]
    public function testAddedDumpChannelIsConfiguredCorrectly(array $dumpChannel): void
    {
        // Check if the dump channel is configured correctly
        $this->assertNotNull($dumpChannel, 'Dump channel configuration should not be null.');
        $this->assertEquals(expected: 'dump',
                            actual  : $dumpChannel['driver'],
                            message : 'Dump channel driver should be "dump".');
        $this->assertCount(1, $dumpChannel);
    }

    public function testAddedDumpChannelHasOneHandler(): DumpHandler
    {
        // Boot the package
        $this->app->register(ServiceProvider::class);

        $handlers = $this->app->get('log')->channel('dump')->getLogger()->getHandlers();
        $this->assertCount(1, $handlers);
        $handler = $handlers[0];
        $this->assertInstanceOf(expected: DumpHandler::class,
                                actual  : $handler,
                                message : 'The handler for the dump channel should be an instance of DumpHandler.');
        return $handler;
    }

    #[Depends('testAddedDumpChannelHasOneHandler')]
    public function testHanlderHasDefaultLevelOfDebug(DumpHandler $handler): void
    {
        // Check if the handler has the default level of debug
        $this->assertEquals(expected: Level::Debug,
                            actual  : $handler->getLevel(),
                            message : 'The handler should have a default level of debug.');
    }

    #[Depends('testAddedDumpChannelHasOneHandler')]
    public function testAddedDumpChannelHasDefaultFormatter(): void
    {
        // Boot the package
        $this->app->register(ServiceProvider::class);

        $formatter = $this->app->get('log')->channel('dump')->getLogger()->getHandlers()[0]->getFormatter();
        $this->assertInstanceOf(expected: LineFormatter::class,
                                actual  : $formatter,
                                message : 'The formatter for the dump channel should be an instance of LineFormatter.');
    }

    public function testDumpChannelCanAcceptCustomLevel(): void
    {
        // Boot the package with a custom level
        $this->app->register(ServiceProvider::class);
        config(['logging.channels.dump.level' => 'info']);

        $handler = $this->app->get('log')->channel('dump')->getLogger()->getHandlers()[0];
        $this->assertEquals(expected: Level::Info,
                            actual  : $handler->getLevel(),
                            message : 'The handler should have a level of info.');
    }

    public function testDumpChannelCanAcceptCustomFormatter(): void
    {
        // Boot the package with a custom formatter
        $this->app->register(ServiceProvider::class);
        config(['logging.channels.dump.formatter' => LineFormatter::class]);

        $handler = $this->app->get('log')->channel('dump')->getLogger()->getHandlers()[0];
        $this->assertInstanceOf(expected: LineFormatter::class,
                                actual  : $handler->getFormatter(),
                                message : 'The handler should have a custom formatter.');
    }

    public function testDumpChannelCanAcceptCustomProcessors(): void
    {
        // Boot the package with a custom processor
        $this->app->register(ServiceProvider::class);
        $dummyProcessor = $this->createMock(WebProcessor::class);
        config(['logging.channels.dump.processors' => [$dummyProcessor]]);

        $logger = $this->app->get('log')->channel('dump')->getLogger();
        $this->assertNotEmpty($logger->getProcessors(),
                              'The logger should have processors configured.');
        $this->assertContains(needle  : $dummyProcessor,
                              haystack: $logger->getProcessors(),
                              message : 'The logger should contain the custom processor.');
    }
}
