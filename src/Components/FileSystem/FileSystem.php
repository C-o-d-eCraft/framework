<?php

namespace Craft\Components\FileSystem;

use Craft\Contracts\FileSystemInterface;
use RuntimeException;
use InvalidArgumentException;

/**
 * Класс FileSystem предоставляет методы для работы с файловой системой,
 * включая запись, чтение, проверку существования файлов и создание директорий.
 */


class FileSystem implements FileSystemInterface
{

    public function getDirName(): string
    {
        $config = require 'config/file-system-config.php';
        return $config['runtime_path'];
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

    /**
     * Читает содержимое файла.
     *
     * @param string $path Путь к файлу.
     *
     * @return string Содержимое файла.
     *
     * @throws RuntimeException Если файл не удалось прочитать.
     */
    public function get(string $path): string
    {
        if (file_exists($path) === false) {
            throw new InvalidArgumentException("Файл не существует: $path");
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException("Не удалось прочитать файл: $path");
        }

        return $content;
    }

    /**
     * Проверяет существование файла.
     *
     * @param string $path Путь к файлу.
     *
     * @return bool True, если файл существует, иначе false.
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Удаляет файл.
     *
     * @param string $path Путь к файлу.
     *
     * @return void
     *
     * @throws RuntimeException Если файл не удалось удалить.
     */
    public function delete(string $path): void
    {
        if (file_exists($path) === false) {
            throw new InvalidArgumentException("Файл не существует: $path");
        }

        if (unlink($path) === false) {
            throw new RuntimeException("Не удалось удалить файл: $path");
        }
    }

    /**
     * Создает директорию.
     *
     * @param string $path Путь к директории.
     * @param int $mode Режим доступа к директории (по умолчанию 0777).
     * @param bool $recursive Разрешить создание вложенных директорий (по умолчанию false).
     *
     * @return void
     *
     * @throws RuntimeException Если директорию не удалось создать.
     */
    public function makeDirectory(string $path, int $mode = 0777, bool $recursive = false): void
    {
        if (mkdir($path, $mode, $recursive) === false && is_dir($path) === false) {
            throw new RuntimeException("Не удалось создать директорию: $path");
        }
    }

    /**
     * Удаляет директорию.
     *
     * @param string $path Путь к директории.
     *
     * @return void
     *
     * @throws RuntimeException Если директорию не удалось удалить.
     */
    public function removeDirectory(string $path): void
    {
        if (is_dir($path) === false) {
            throw new InvalidArgumentException("Директория не существует: $path");
        }

        if (rmdir($path) === false) {
            throw new RuntimeException("Не удалось удалить директорию: $path");
        }
    }

}