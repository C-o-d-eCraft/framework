<?php

namespace Craft\Contracts;

use Craft\Http\Exceptions\ForbiddenHttpException;
use Craft\Http\ResponseTypes\CreateResponse;
use Craft\Http\ResponseTypes\DeleteResponse;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\ResponseTypes\PatchResponse;
use Craft\Http\ResponseTypes\UpdateResponse;
use Craft\Http\Validator\AbstractFormRequest;

interface ResourceControllerInterface
{
    /**
     * @param string|null $id
     * @return JsonResponse
     */
    public function actionGetList(?string $id): JsonResponse;

    /**
     * @return JsonResponse
     * @throws ForbiddenHttpException
     */
    public function actionGetItem(string|int $id, string|int $itemId): JsonResponse;

    /**
     * @return CreateResponse
     * @throws ForbiddenHttpException
     */
    public function actionCreate(string|int $id): CreateResponse;

    /**
     * @param string|int $id
     * @return UpdateResponse
     * @throws ForbiddenHttpException
     */
    public function actionUpdate(string|int $id, string|int $itemId): UpdateResponse;

    /**
     * @param string|int $id
     * @return PatchResponse
     * @throws ForbiddenHttpException
     */
    public function actionPatch(string|int $id, string|int $itemId): PatchResponse;

    /**
     * @param string|int $id
     * @return DeleteResponse
     * @throws ForbiddenHttpException
     */
    public function actionDelete(string|int $id): DeleteResponse;
}
