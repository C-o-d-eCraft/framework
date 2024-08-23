<?php

namespace Craft\Http\Controllers;

use Craft\Contracts\ResourceControllerInterface;
use Craft\Http\Exceptions\ForbiddenHttpException;
use Craft\Http\ResponseTypes\CreateResponse;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\ResponseTypes\PatchResponse;
use Craft\Http\ResponseTypes\UpdateResponse;
use Craft\Http\ResponseTypes\DeleteResponse;
use Craft\Http\Validator\AbstractFormRequest;

abstract class ResourceController implements ResourceControllerInterface
{
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
    public function actionGetList(): JsonResponse
    {
        return new JsonResponse($this->getList());
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
    public function actionGetItem(): JsonResponse
    {
        return new JsonResponse($this->getItem());
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
        $form = $this->formRequestFactory->create($this->forms[self::CREATE]);

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
    protected function update(string|int $id, AbstractFormRequest $form): void
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
        $form = $this->formRequestFactory->create($this->forms[self::UPDATE]);

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
        $form = $this->formRequestFactory->create($this->forms[self::PATCH]);

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
