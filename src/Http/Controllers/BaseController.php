<?php

namespace Craft\Http\Controllers;

use Craft\Components\ErrorHandler\StatusCodeEnum;
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
     * Возвращает успешный ответ 200 OK.
     *
     * @param mixed $data Данные для ответа.
     * @return JsonResponse
     */
    protected function okResponse(mixed $data): JsonResponse
    {
        return $this->jsonResponse($data, StatusCodeEnum::OK);
    }

    /**
     * Возвращает успешный ответ 201 Created.
     *
     * @return JsonResponse
     */
    protected function createdResponse(): JsonResponse
    {
        return $this->jsonResponse([], StatusCodeEnum::CREATED);
    }

    /**
     * Возвращает успешный ответ 204 No Content.
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return $this->jsonResponse([], StatusCodeEnum::NO_CONTENT);
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
        throw new HttpException(StatusCodeEnum::FORBIDDEN, 'GET method "actionGetList" not implemented');
    }

    /**
     * Получить 1 элемент
     * Обработка GET запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionGetItem(): JsonResponse
    {
        throw new HttpException(StatusCodeEnum::FORBIDDEN, 'GET method "actionGetItem"  not implemented');
    }

    /**
     * Создать 1 элемент
     * Обработка POST запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionCreateItem(): JsonResponse
    {
        throw new HttpException(StatusCodeEnum::FORBIDDEN, 'POST method "actionCreateItem" not implemented');
    }

    /**
     * Полное изменение элемента (кроме ID)
     * Обработка PUT запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionFullUpdateItem(): JsonResponse
    {
        throw new HttpException(StatusCodeEnum::FORBIDDEN, 'PUT method "actionFullUpdateItem" not implemented');
    }

    /**
     * Частичное изменение элемента
     * Обработка PATCH запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionPartialUpdateItem(): JsonResponse
    {
        throw new HttpException(StatusCodeEnum::FORBIDDEN, 'PATCH method "actionPartialUpdateItem" not implemented');
    }

    /**
     * Удаление элемента
     * Обработка DELETE запроса.
     * Должен быть реализован в дочернем классе.
     *
     * @return JsonResponse
     * @throws HttpException
     */
    public function actionDeleteItem(): JsonResponse
    {
        throw new HttpException(StatusCodeEnum::FORBIDDEN, 'DELETE method "actionDeleteItem" not implemented');
    }
}
