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

    protected function __construct(private readonly array $config = []) { }

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
        if (empty(self::$instance) === false) {
            return self::$instance;
        }

        return self::$instance = new self($config);
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
        if ($dependencyName === self::class) {
            return $this;
        }

        $className = $this->config[$dependencyName] ?? $dependencyName;

        if (is_callable($className) === true) {
            return $this->config[$dependencyName]($this);
        }

        $reflectionClass = new ReflectionClass($className);

        if ($reflectionClass->isInstantiable() === false || $reflectionClass->isCloneable() === false) {
            throw new ReflectionException('Экземпляр класса ' . $className . ' не может быть создан');
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        if (is_object($className) === true) {
            return $className;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            if (interface_exists($parameter->getType()->getName()) === false && class_exists($parameter->getType()->getName()) === false) {
                continue;
            }

            $dependencyInterface = $parameter->getType()->getName();
            
            $dependencies[] = $this->make($dependencyInterface);
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
        if (isset($this->singletons[$contract])) {
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
        if (is_callable($handler)) {
            return $handler(...$args);
        }

        if (is_object($handler)) {
            $reflection = new ReflectionMethod($handler, $method);
            $parameters = $reflection->getParameters();
            $resolvedArgs = $this->resolveArguments($parameters);
            
            $args = array_merge($resolvedArgs, $args);
            
            return $reflection->invokeArgs($handler, $args);
        }

        if (is_string($handler) && class_exists($handler)) {
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

            if (empty($dependencyName) || $parameter->getType()->isBuiltin() || !isset($this->config[$dependencyName])) {
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
        return isset($this->config[$contract]);
    }
}
