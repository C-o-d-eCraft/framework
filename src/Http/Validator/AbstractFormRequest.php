<?php

namespace Craft\Http\Validator;

use Craft\Http\Exceptions\BadRequestHttpException;

abstract class AbstractFormRequest
{
    public array $errors = [];
    protected Validator $validator;

    public function __construct(array $formData)
    {
        $this->validator = new Validator($formData);
    }

    abstract public function rules(): array;

    public function validate(): void
    {
        foreach ($this->rules() as $rule) {
            try {
                $this->validator->apply($rule);
            } catch (BadRequestHttpException $e) {
                $this->addError($rule[0], $e->getMessage());
            }
        }

        if (empty($this->getErrors()) === false) {
            throw new BadRequestHttpException($this->getErrorsAsString());
        }
    }

    protected function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    public function getErrors(): array
    {
        $this->errors = array_merge($this->errors, $this->validator->errors);

        return $this->errors;
    }

    public function getErrorsAsString(): string
    {
        $errorMessages = [];

        foreach ($this->getErrors() as $attributeErrors) {
            foreach ($attributeErrors as $errorMessage) {
                $errorMessages[] = $errorMessage;
            }
        }

        return implode(' ', $errorMessages);
    }
}
