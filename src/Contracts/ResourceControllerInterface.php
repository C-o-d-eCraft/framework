<?php

namespace Craft\Contracts;

use Craft\Http\Exceptions\ForbiddenHttpException;
use Craft\Http\ResponseTypes\CreateResponse;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\ResponseTypes\PatchResponse;
use Craft\Http\ResponseTypes\UpdateResponse;
use Craft\Http\ResponseTypes\DeleteResponse;
use Craft\Http\Validator\AbstractFormRequest;

interface ResourceControllerInterface
{
    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function getList(): array;

    /**
     * @return JsonResponse
     * @throws ForbiddenHttpException
     */
    public function actionGetList(): JsonResponse;

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function getItem(): array;

    /**
     * @return JsonResponse
     * @throws ForbiddenHttpException
     */
    public function actionGetItem(): JsonResponse;

    /**
     * @param AbstractFormRequest $form
     * @return void
     * @throws ForbiddenHttpException
     */
    public function create(AbstractFormRequest $form): void;

    /**
     * @return CreateResponse
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): CreateResponse;

    /**
     * @param string|int $id
     * @param AbstractFormRequest $form
     * @return void
     * @throws ForbiddenHttpException
     */
    public function update(string|int $id, AbstractFormRequest $form): void;

    /**
     * @param string|int $id
     * @return UpdateResponse
     * @throws ForbiddenHttpException
     */
    public function actionUpdate(string|int $id): UpdateResponse;

    /**
     * @param string|int $id
     * @param AbstractFormRequest $form
     * @return void
     * @throws ForbiddenHttpException
     */
    public function patch(string|int $id, AbstractFormRequest $form): void;

    /**
     * @param string|int $id
     * @return PatchResponse
     * @throws ForbiddenHttpException
     */
    public function actionPatch(string|int $id): PatchResponse;

    /**
     * @param string|int $id
     * @return void
     * @throws ForbiddenHttpException
     */
    public function delete(string|int $id): void;

    /**
     * @param string|int $id
     * @return DeleteResponse
     * @throws ForbiddenHttpException
     */
    public function actionDelete(string|int $id): DeleteResponse;
}