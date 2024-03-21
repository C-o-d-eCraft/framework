<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\HttpKernelInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Http\Factory\RequestFactory;
use PHPUnit\Framework\MockObject\Exception;
use ReflectionException;
use Codeception\{Test\Unit};

class DIContainerTest extends Unit
{
    private function createContainerSpy(array $config = []): DIContainer
    {
        return new class($config) extends DIContainer
        {
            public function __construct($config)
            {
                parent::__construct($config);
                static::$instance = null;
            }
        };
    }

    public function testCreateContainer(): void
    {
        $container = DIContainer::createContainer();

        $this->assertInstanceOf(DIContainer::class, $container);
    }

    public function testCreateContainerTwice(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Контейнер уже инициализирован!');

        DIContainer::createContainer();
        DIContainer::createContainer();
    }

    public function testCloneThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Клонирование запрещено!');

        $container = $this->createContainerSpy();
        clone $container;
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testRegisterSingleton(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $container = $this->createContainerSpy([ResponseInterface::class => $expectedResponse]);

        $container->singleton(ResponseInterface::class);

        $this->assertEquals($expectedResponse, $container->singletons[ResponseInterface::class]);
    }

    /**
     * @throws ReflectionException
     */
    public function testRegisterSingletonForContractNotInConfig(): void
    {
        $container = $this->createContainerSpy();

        $this->expectException(\Exception::class);
        $container->singleton(ResponseInterface::class);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testRegisterDefinition(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $container = $this->createContainerSpy([ResponseInterface::class => $responseMock]);

        $firstInstance = $container->make(ResponseInterface::class);
        $secondInstance = $container->make(ResponseInterface::class);

        $this->assertInstanceOf(ResponseInterface::class, $firstInstance);
        $this->assertNotSame($firstInstance, $secondInstance);
    }

    /**
     * @throws ReflectionException
     */
    public function testRegisterDefinitionForContractNotInConfig(): void
    {
        $container = $this->createContainerSpy();

        $this->expectException(\Exception::class);
        $container->make(ResponseInterface::class);
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testCallWithExistingMethod(): void
    {
        $container = $this->createContainerSpy([RequestInterface::class => fn() => RequestFactory::createRequest()]);

        $responseStub = $this->createStub(ResponseInterface::class);
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $kernelMock->expects($this->once())->method('handle')->willReturn($responseStub);

        $this->assertEquals($responseStub, $container->call($kernelMock, 'handle'));
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testCallWithNotExistingMethod(): void
    {
        $container = $this->createContainerSpy();

        $responseMock = $this->createMock(ResponseInterface::class);

        $this->expectException(\Exception::class);
        $container->call($responseMock, 'getSomeMethod');
    }
}
