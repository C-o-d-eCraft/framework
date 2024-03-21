<?php

namespace Craft\Components\DIContainer;

use Craft\Contracts\ContainerInterface;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use LogicException;

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
            throw new LogicException('Контейнер уже инициализирован!');
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
        $className = $this->config[$dependencyName] ?? $dependencyName;

        if (is_callable($className) === true) {
            return $this->config[$dependencyName]($this);
        }

        $reflectionClass = new ReflectionClass($className);

        if ($reflectionClass->isInstantiable() === false || $reflectionClass->isCloneable() === false){
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
     * @param callable|object$handler Обработчик
     * @param string $method Имя метода
     * @return mixed Результат выполнения обработчика
     * @throws InvalidArgumentException Если при вызове метода класса не передано имя метода
     * @throws ReflectionException Если не удается создать экземпляр класса или получить информацию о методе или функции
     */
    public function call(callable|object $handler, string $method): mixed
    {
        $reflection = is_callable($handler) ? new ReflectionFunction($handler) : new ReflectionMethod($handler, $method);

        $parameters = $reflection->getParameters();

        $arguments = [];

        foreach ($parameters as $parameter) {
            $dependencyName = $parameter->getType()->getName();

            if (empty($dependencyName)) {
                continue;
            }

            if ($parameter->getType()->isBuiltin()) {
                continue;
            }

            $argument = $this->config[$dependencyName];

            if (is_callable($argument)) {
                $arguments[] = $argument($this);
                continue;
            }

            $arguments[] = $this->build($this->config[$dependencyName]);
        }

        if (is_callable($handler)) {
            return $handler(...$arguments);
        }

        if (is_object($handler)) {
            return $handler->{$method}(...$arguments);
        }

        return $this->build($handler)->{$method}(...$arguments);
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
