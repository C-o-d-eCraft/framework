<?php

namespace Craft\Contracts;

use Craft\Http\ResponseTypes\CreateResponse;
use Craft\Http\ResponseTypes\DeleteResponse;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\ResponseTypes\PatchResponse;
use Craft\Http\ResponseTypes\UpdateResponse;

interface ResourceControllerInterface
{
    /**
     * @param string|null $id
     * @return JsonResponse
     */
    public function actionGetList(?string $id = null): JsonResponse;

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return JsonResponse
     */
    public function actionGetItem(string|int $id, ?string $itemId = null): JsonResponse;

    /**
     * @param string|null $id
     * @return CreateResponse
     */
    public function actionCreate(?string $id = null): CreateResponse;

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return UpdateResponse
     */
    public function actionUpdate(string|int $id, ?string $itemId = null): UpdateResponse;

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return PatchResponse
     */
    public function actionPatch(string|int $id, ?string $itemId = null): PatchResponse;

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return DeleteResponse
     */
    public function actionDelete(string|int $id, ?string $itemId = null): DeleteResponse;
}
