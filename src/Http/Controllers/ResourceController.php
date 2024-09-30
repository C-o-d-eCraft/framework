<?php

namespace Craft\Http\Controllers;

use Craft\Contracts\FormRequestFactoryInterface;
use Craft\Contracts\ResourceControllerInterface;
use Craft\Http\Exceptions\BadRequestHttpException;
use Craft\Http\Exceptions\ForbiddenHttpException;
use Craft\Http\Message\Stream;
use Craft\Http\ResponseTypes\CreateResponse;
use Craft\Http\ResponseTypes\DeleteResponse;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\ResponseTypes\PatchResponse;
use Craft\Http\ResponseTypes\UpdateResponse;
use Craft\Http\Validator\AbstractFormRequest;

abstract class ResourceController implements ResourceControllerInterface
{
    protected const CREATE = 'create';
    protected const UPDATE = 'update';
    protected const PATCH = 'patch';

    public function __construct(private FormRequestFactoryInterface $formRequestFactory, private array $forms) { }

    /**
     * @param string|null $id
     * @return array
     * @throws ForbiddenHttpException
     */
    protected function getList(?string $id): array
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|null $id
     * @return JsonResponse
     * @throws ForbiddenHttpException
     */
    public function actionGetList(?string $id = null): JsonResponse
    {
        return new JsonResponse($this->getList($id));
    }

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return array
     * @throws ForbiddenHttpException
     */
    protected function getItem(string|int $id, ?string $itemId): array
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return JsonResponse
     * @throws ForbiddenHttpException
     */
    public function actionGetItem(string|int $id, ?string $itemId = null): JsonResponse
    {
        return new JsonResponse($this->getItem($id, $itemId));
    }

    /**
     * @param string|null $id
     * @param AbstractFormRequest $form
     * @return void
     * @throws ForbiddenHttpException
     */
    protected function create(?string $id, AbstractFormRequest $form): void
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|null $id
     * @return CreateResponse
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionCreate(?string $id = null): CreateResponse
    {
        $form = $this->formRequestFactory->create($this->forms[self::CREATE]);
        
        $form->validate();

        if (empty($form->getErrors()) === false) {
            throw new BadRequestHttpException($form->getErrors());
        }

        $this->create($id, $form);

        $response = new CreateResponse();

        if ($form->responseData !== []) {
            $response->setBody(new Stream(json_encode($form->responseData)));
        }

        return $response;
    }

    /**
     * @param string|int $id
     * @param AbstractFormRequest $form
     * @param string|null $itemId
     * @return void
     * @throws ForbiddenHttpException
     */
    protected function update(string|int $id, AbstractFormRequest $form, ?string $itemId): void
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return UpdateResponse
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionUpdate(string|int $id, ?string $itemId = null): UpdateResponse
    {
        $this->validateResourceExists($id, $itemId);

        $form = $this->formRequestFactory->create($this->forms[self::UPDATE]);

        $form->validate();

        if (empty($form->getErrors()) === false) {
            throw new BadRequestHttpException($form->getErrors());
        }

        $this->update($id, $form, $itemId);

        return new UpdateResponse();
    }

    /**
     * @param string|int $id
     * @param AbstractFormRequest $form
     * @param string|null $itemId
     * @return void
     * @throws ForbiddenHttpException
     */
    protected function patch(string|int $id, AbstractFormRequest $form, ?string $itemId): void
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return PatchResponse
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionPatch(string|int $id, ?string $itemId = null): PatchResponse
    {
        $this->validateResourceExists($id, $itemId);

        $form = $this->formRequestFactory->create($this->forms[self::PATCH]);

        $form->setSkipEmptyValues();

        $form->validate();

        if (empty($form->getErrors()) === false) {
            throw new BadRequestHttpException($form->getErrors());
        }

        $this->patch($id, $form, $itemId);

        return new PatchResponse();
    }

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return void
     * @throws ForbiddenHttpException
     */
    protected function delete(string|int $id, ?string $itemId): void
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return DeleteResponse
     * @throws ForbiddenHttpException
     */
    public function actionDelete(string|int $id, ?string $itemId = null): DeleteResponse
    {
        $this->delete($id, $itemId);

        return new DeleteResponse();
    }

    /**
     * @param string|int $id
     * @param string|null $itemId
     * @return void
     */
    protected function validateResourceExists(string|int $id, ?string $itemId): void
    {
        // ...
    }
}
