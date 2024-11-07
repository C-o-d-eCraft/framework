<?php

namespace Craft\Http\Route;

use Craft\Http\Route\DTO\RouteParamsDTO;

class RouteParamsParser
{
    /**
     * Строит регулярное выражение из строки маршрута
     *
     * @param string $route
     * @return string
     */
    public function buildRegexFromRoute(string $route): string
    {
        $pattern = preg_replace_callback('/\{(\w+)(:[^}]+)?\}/', fn($matches) => '([^/]+)', strtok($route, '?'));

        return '#^' . $pattern . '$#';
    }

    /**
     * Парсит параметры маршрута из совпадений
     *
     * @param string $route
     * @param array $matches
     * @return \Craft\Http\Route\RouteParams
     */
    public function parseRouteParameters(string $route, array $matches): array
    {
        preg_match_all('/\{(\w+)(:[^}]+)?\}/', $route, $parameterDefinitions);

        return $this->generateParamsArray($parameterDefinitions[1], $matches, $parameterDefinitions[2]);
    }

    /**
     * @param string $route
     * @return bool
     */
    public function hasQueryParameters(string $route): bool
    {
        return strpos($route, '?') !== false;
    }

    /**
     * Парсит спецификаторы для query-параметров из маршрута
     *
     * @param string $route
     * @param array $queryParams
     * @return \Craft\Http\Route\RouteParams
     */
    public function parseQueryParameters(string $route, array $queryParams): array
    {
        $routeParts = explode('?', $route);
        $queryParametersString = $routeParts[1] ?? '';
        $queryParameters = [];

        foreach (explode('&', $queryParametersString) as $param) {
            if (empty($param) === true) {
                continue;
            }

            [$parameterName, $specifiersString] = explode(':', $param . ':', 2);
            $specifiers = $this->parseSpecifiers($specifiersString);

            $queryParameters[$parameterName] = new RouteParamsDTO($parameterName, $queryParams[$parameterName] ?? null, $specifiers);
        }

        return $queryParameters;
    }

    /**
     * Разбирает спецификаторы в массив
     *
     * @param string $specifiers
     * @return array
     */
    private function parseSpecifiers(string $specifiers): array
    {
        return array_filter(explode('|', trim($specifiers, ':')), 'strlen');
    }

    /**
     * Создает массив параметров для маршрута
     *
     * @param array $parameterNames
     * @param array $values
     * @param array $specifiersList
     * @return array
     */
    private function generateParamsArray(array $parameterNames, array $values, array $specifiersList): array
    {
        $params = [];
        foreach ($parameterNames as $index => $name) {
            $specifiers = $this->parseSpecifiers($specifiersList[$index] ?? '');
            $params[$name] = new RouteParamsDTO($name, $values[$index + 1] ?? null, $specifiers);
        }

        return $params;
    }
}