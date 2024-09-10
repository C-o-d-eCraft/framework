<?php

namespace Craft\Components\FileSystem;

use Craft\Contracts\FileSystemInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Класс FileSystem предоставляет методы для работы с файловой системой,
 * запись, чтение, проверку существования файлов и создание директорий.
 */
class FileSystem implements FileSystemInterface
{
    public array $aliasStorage;

    public function __construct(array $config)
    {
        $this->aliasStorage = $config;
    }

    public function getAlias(string $name): string
    {
        return $this->aliasStorage[$name] ?? throw new InvalidArgumentException("Путь {$name} не найден");
    }

    /**
     * Записывает содержимое в файл.
     *
     * @param string $path Путь к файлу.
     * @param string $content Содержимое, которое нужно записать в файл.
     * @param int $flags Опциональные флаги (по умолчанию 0). Подробнее см. в документации к file_put_contents.
     *
     * @return void
     *
     * @throws RuntimeException Если файл не удалось записать.
     */
    public function put(string $path, string $content, int $flags = 0): void
    {
        $result = file_put_contents($path, $content, $flags);

        if ($result === false) {
            throw new RuntimeException("Не удалось записать данные в файл: $path");
        }
    }
}
