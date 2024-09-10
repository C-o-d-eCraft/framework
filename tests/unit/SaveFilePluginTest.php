<?php

namespace Tests\Unit;

use Craft\Console\Plugins\SaveFilePlugin;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\FileSystemInterface;
use Craft\Contracts\OutputInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SaveFilePluginTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testUpdateSuccessfullyWritesToFile()
    {
        $fileSystemStub = $this->createStub(FileSystemInterface::class);

        $fileSystemStub->method('put');
        $fileSystemStub->method('getAlias')->willReturn('/var/www/html/runtime/console-output');

        $outputStub = $this->createStub(OutputInterface::class);

        $eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);

        $plugin = new SaveFilePlugin($eventDispatcherStub, $outputStub, $fileSystemStub);

        $this->expectNotToPerformAssertions();

        $plugin->update('testMessage');
    }

    /**
     * @throws Exception
     */
    public function testUpdateThrowsExceptionOnWriteFailure()
    {
        $fileSystemStub = $this->createStub(FileSystemInterface::class);

        $fileSystemStub->method('put')->willThrowException(new RuntimeException('Не удалось записать данные в файл'));
        $fileSystemStub->method('getAlias')->willReturn('/var/www/html/runtime/console-output');

        $outputStub = $this->createStub(OutputInterface::class);

        $eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);

        $plugin = new SaveFilePlugin($eventDispatcherStub, $outputStub, $fileSystemStub);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Не удалось записать данные в файл');

        $plugin->update('testMessage');
    }
}
