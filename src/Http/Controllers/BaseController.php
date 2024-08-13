<?php

namespace Craft\Http\Controllers;

use Craft\Http\Exceptions\ForbiddenHttpException;
use Craft\Http\Exceptions\HttpException;
use Craft\Http\ResponseTypes\JsonResponse;

/**
 * Базовый абстрактный класс контроллера.
 * Содержит базовые методы для обработки запросов и возвращения стандартных статус-кодов.
 * Дочерние классы должны реализовать методы для обработки GET, POST, PUT, PATCH и DELETE запросов.
 */
abstract class BaseController
{
    /**
     * Создает JSON ответ.
     *
     * @param mixed $data Данные для JSON ответа.
     * @param int $statusCode Статус-код HTTP ответа.
     * @return JsonResponse
     */
    protected function jsonResponse(mixed $data, int $statusCode): JsonResponse
    {
        $response = new JsonResponse();
        $response->setJsonBody($data);
        $response->setStatusCode($statusCode);

        return $response;
    }

    /**
     * Возвращает ответ при выбросе HttpException
     *
     * @param HttpException $httpException Исключение
     * @param array $data Данные для ответа.
     * @return JsonResponse
     */
    protected function httpExceptionResponse(HttpException $httpException, array $data = []): JsonResponse
    {
        return $this->jsonResponse([
            'cause' => $httpException->getMessage(),
            'type' => $httpException->getType(),
            'data' => $data,
        ], $httpException->getCode());
    }

    /**
     * Получить список (более 1 элемента)
     * Обработка GET запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionGetList(): JsonResponse
    {
        $this->getList();
    }

    /**
     * @throws HttpException
     */
    protected function getList(): void
    {
        throw new ForbiddenHttpException('GET method "actionGetList" not implemented');
    }

    /**
     * Получить 1 элемент
     * Обработка GET запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionGetItem(int $id): JsonResponse
    {
        $this->getItem();
    }

    /**
     * @throws HttpException
     */
    protected function getItem(): HttpException
    {
        throw new ForbiddenHttpException('GET method "actionGetItem" not implemented');
    }

    /**
     * Создать 1 элемент
     * Обработка POST запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionCreate(): JsonResponse
    {
        $this->create();
    }

    /**
     * @throws HttpException
     */
    protected function create(): HttpException
    {
        throw new ForbiddenHttpException('POST method "actionCreate" not implemented');
    }

    /**
     * Полное изменение элемента (кроме ID)
     * Обработка PUT запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionPut(int $id): JsonResponse
    {
        $this->put();
    }

    /**
     * @throws HttpException
     */
    protected function put(): void
    {
        throw new ForbiddenHttpException('PUT method "actionPut" not implemented');
    }

    /**
     * Частичное изменение элемента
     * Обработка PATCH запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionPatch(int $id): JsonResponse
    {
        $this->patch();
    }

    /**
     * @throws HttpException
     */
    protected function patch(): void
    {
        throw new ForbiddenHttpException('PATCH method "actionPatch" not implemented');
    }

    /**
     * Удаление элемента
     * Обработка DELETE запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionDelete(int $id): JsonResponse
    {
        $this->delete();
    }

    /**
     * @throws HttpException
     */
    protected function delete(): void
    {
        throw new ForbiddenHttpException('DELETE method "actionDelete" not implemented');
    }
}
