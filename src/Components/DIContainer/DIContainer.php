<?php

namespace Craft\Components\DIContainer;

use Craft\Contracts\ContainerInterface;
use Exception;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class DIContainer implements ContainerInterface
{
    /**
     * @var self|null Экземпляр класса DIContainer
     */
    protected static ?self $instance = null;
    public array $singletons = [];

    protected function __construct(private readonly array $config = [])
    {
        $this->registerSingletons();
    }

    /**
     * Запрещает клонирование объектов класса DIContainer
     *
     * @throws LogicException
     */
    public function __clone(): void
    {
        throw new LogicException('Клонирование запрещено!');
    }

    /**
     * Создает экземпляр класса DIContainer
     *
     * @param array $config Массив конфигурации
     * @return DIContainer Экземпляр класса DIContainer
     */
    public static function createContainer(array $config = []): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        return self::$instance = new self($config);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    private function registerSingletons(): void
    {
        if (empty($this->config['singletons']) === true) {
            return;
        }

        foreach ($this->config['singletons'] as $contract) {
            $this->singleton($contract);
        }
    }

    /**
     * Регистрирует контракт как синглтон.
     *
     * @param string $contract Имя контракта
     * @throws ReflectionException
     */
    public function singleton(string $contract): void
    {
        if (isset($this->singletons[$contract]) === false) {
            $this->singletons[$contract] = $this->build($contract);
        }
    }

    /**
     * Создание экземпляра объекта в зависимости от имени класса
     *
     * @param string $dependencyName Имя зависимости, для которой нужно создать объект
     * @return object Возвращает экземпляр объекта в зависимости от имени класса
     * @throws InvalidArgumentException Если класс не существует
     * @throws ReflectionException Если экземпляр класса не может быть создан
     */
    public function build(string $dependencyName): object
    {
        if ($dependencyName === self::class || $dependencyName === ContainerInterface::class) {
            return $this;
        }

        $configEntry = $this->config[$dependencyName] ?? $dependencyName;

        if (is_callable($configEntry) === true) {
            return $configEntry($this);
        }

        $params = [];
        $className = $configEntry;

        if (is_array($configEntry) === true) {
            $className = $configEntry[0];
            $params = $configEntry[1] ?? [];
        }

        $reflectionClass = new ReflectionClass($className);

        if ($reflectionClass->isInstantiable() === false || $reflectionClass->isCloneable() === false) {
            throw new ReflectionException('Экземпляр класса ' . $className . ' не может быть создан');
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            $paramType = $parameter->getType();

            if ($paramType instanceof \ReflectionNamedType === true && $paramType->isBuiltin() === false) {
                $dependencyInterface = $paramType->getName();
                $dependencies[] = $this->make($dependencyInterface);

                continue;
            }

            if (array_key_exists($paramName, $params) === true) {
                $dependencies[] = $params[$paramName];

                continue;
            }

            if ($parameter->isDefaultValueAvailable() === true) {
                $dependencies[] = $parameter->getDefaultValue();

                continue;
            }

            throw new InvalidArgumentException("Невозможно задать параметр '$paramName' для класса '$className'");
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * Создает экземпляр класса, реализующего указанный интерфейс, и сохраняет его в качестве синглтона.
     *
     * @param string $contract Имя контракта
     * @return object Экземпляр класса, реализующего указанный интерфейс и сохраненный в качестве синглтона
     * @throws ReflectionException Если экземпляр класса не может быть создан
     * @throws Exception Если зависимость для указанного контракта не задана в конфигурационном файле
     */
    public function make(string $contract): object
    {
        if (isset($this->singletons[$contract]) === true) {
            return $this->singletons[$contract];
        }

        return $this->build($contract);
    }

    /**
     * Выполняет вызов указанного обработчика (callable или объекта) с передачей аргументов.
     *
     * @param callable|object|string $handler Обработчик
     * @param string $method Имя метода
     * @param array $args Аргументы метода (по умолчанию пустой массив)
     * @return mixed Результат выполнения обработчика
     * @throws InvalidArgumentException Если при вызове метода класса не передано имя метода
     * @throws ReflectionException Если не удается создать экземпляр класса или получить информацию о методе или функции
     */
    public function call(callable|object|string $handler, string $method, array $args = []): mixed
    {
        if (is_callable($handler) === true) {
            return $handler(...$args);
        }

        if (is_object($handler) === true) {
            $reflection = new ReflectionMethod($handler, $method);
            $parameters = $reflection->getParameters();
            $resolvedArgs = $this->resolveArguments($parameters);

            $args = array_merge($resolvedArgs, $args);

            return $reflection->invokeArgs($handler, $args);
        }

        if (is_string($handler) === true && class_exists($handler) === true) {
            $instance = $this->make($handler);

            $reflection = new ReflectionMethod($instance, $method);
            $parameters = $reflection->getParameters();
            $resolvedArgs = $this->resolveArguments($parameters);

            $args = array_merge($resolvedArgs, $args);

            return $reflection->invokeArgs($instance, $args);
        }

        throw new InvalidArgumentException('Невозможно выполнить вызов: некорректный обработчик или класс');
    }

    /**
     * @param array $parameters
     * @return array
     * @throws ReflectionException
     */
    private function resolveArguments(array $parameters): array
    {
        $arguments = [];

        foreach ($parameters as $parameter) {
            $dependencyName = $parameter->getType()?->getName();

            if (
                empty($dependencyName) === true
                || $parameter->getType()->isBuiltin() === true
                || isset($this->config[$dependencyName]) === false
            ) {
                continue;
            }

            $argument = $this->config[$dependencyName];

            if (is_callable($argument)) {
                $arguments[] = $argument($this);

                continue;
            }

            $arguments[] = $this->build($this->config[$dependencyName]);
        }

        return $arguments;
    }

    /**
     * Проверяет наличие контракта в конфигурации.
     *
     * @param string $contract Имя контракта
     * @return bool Возвращает true, если контракт существует в конфигурации
     */
    public function has(string $contract): bool
    {
        return isset($this->config[$contract]) === true;
    }
}
