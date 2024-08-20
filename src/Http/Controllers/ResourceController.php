<?php

namespace Craft\Http\Controllers;

use Craft\Http\Exceptions\ForbiddenHttpException;
use Craft\Http\Exceptions\HttpException;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\Validator\AbstractFormRequest;

/**
 * Базовый абстрактный класс контроллера.
 * Содержит базовые методы для обработки запросов и возвращения стандартных статус-кодов.
 * Дочерние классы должны реализовать методы для обработки GET, POST, PUT, PATCH и DELETE запросов.
 */
abstract class ResourceController
{
    /**
     * Создает JSON ответ.
     *
     * @param mixed $data Данные для JSON ответа.
     * @param int $statusCode Статус-код HTTP ответа.
     * @return JsonResponse
     */
    protected function setJsonResponse(mixed $data, int $statusCode): JsonResponse
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
    protected function setHttpExceptionResponse(HttpException $httpException, array $data = []): JsonResponse
    {
        return $this->setJsonResponse([
            'cause' => $httpException->getMessage(),
            'type' => $httpException->getType(),
            'data' => $data,
        ], $httpException->getCode());
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    protected function getList(): array
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionGetList(): array
    {
        $this->getList();
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    protected function getItem(): array
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionGetItem(): array
    {
        $this->getItem();
    }

    /**
     * @param AbstractFormRequest $form
     * @return void
     * @throws ForbiddenHttpException
     */
    protected function create(AbstractFormRequest $form): void
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @return CreateResponse
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): CreateResponse
    {
        $form = $formRequestFactory->create($this->forms[self::CREATE]);

        $form->validate();

        if (empty($form->getErrors()) === false) {
            throw new BadRequestHttpException($form->getErrors());
        }

        $this->create($form);

        return new CreateResponse();
    }

    /**
     * @param string|int $id
     * @param AbstractFormRequest $form
     * @return array
     * @throws ForbiddenHttpException
     */
    protected function update(string|int $id, AbstractFormRequest $form): array
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|int $id
     * @return UpdateResponse
     * @throws ForbiddenHttpException
     */
    public function actionUpdate(string|int $id): UpdateResponse
    {
        $form = $formRequestFactory->create($this->forms[self::UPDATE]);

        $form->validate();

        if (empty($form->getErrors()) === false) {
            throw new BadRequestHttpException($form->getErrors());
        }

        $this->update($id, $form);

        return new UpdateResponse();
    }

    /**
     * @param string|int $id
     * @param AbstractFormRequest $form
     * @return void
     * @throws ForbiddenHttpException
     */
    protected function patch(string|int $id, AbstractFormRequest $form): void
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|int $id
     * @return PatchResponse
     * @throws ForbiddenHttpException
     */
    public function actionPatch(string|int $id): PatchResponse
    {
        $form = $formRequestFactory->create($this->forms[self::PATCH]);

        $form->setSkipEmptyValues();

        $form->validate();

        if (empty($form->getErrors()) === false) {
            throw new BadRequestHttpException($form->getErrors());
        }

        $this->patch($id, $form);

        return new PatchResponse();
    }

    /**
     * @param string|int $id
     * @return void
     * @throws ForbiddenHttpException
     */
    protected function delete(string|int $id): void
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|int $id
     * @return DeleteResponse
     * @throws ForbiddenHttpException
     */
    public function actionDelete(string|int $id): DeleteResponse
    {
        $this->delete($id);

        return new DeleteResponse();
    }
}
