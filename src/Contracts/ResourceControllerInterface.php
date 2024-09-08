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
     * @return JsonResponse
     * @throws ForbiddenHttpException
     */
    public function actionGetList(string|int $id = null): JsonResponse;

    /**
     * @return JsonResponse
     * @throws ForbiddenHttpException
     */
    public function actionGetItem(string|int $id): JsonResponse;

    /**
     * @return CreateResponse
     * @throws ForbiddenHttpException
     */
    public function actionCreate(string|int $id = null): CreateResponse;

    /**
     * @param string|int $id
     * @return UpdateResponse
     * @throws ForbiddenHttpException
     */
    public function actionUpdate(string|int $id): UpdateResponse;

    /**
     * @param string|int $id
     * @return PatchResponse
     * @throws ForbiddenHttpException
     */
    public function actionPatch(string|int $id): PatchResponse;

    /**
     * @param string|int $id
     * @return DeleteResponse
     * @throws ForbiddenHttpException
     */
    public function actionDelete(string|int $id): DeleteResponse;
}
