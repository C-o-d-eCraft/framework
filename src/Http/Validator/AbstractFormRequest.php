<?php

namespace Craft\Http\Validator;

use Craft\Contracts\RequestInterface;
use InvalidArgumentException;

abstract class AbstractFormRequest
{
    public array $data;
    public array $errors = [];
    protected Validator $validator;

    public function __construct(RequestInterface $request)
    {
        $this->data = $request->getParams();
        $this->validator = new Validator($this->data);
    }

    abstract public function rules(): array;

    public function validate(): void
    {
        foreach ($this->rules() as $rule) {
            try {
                $this->validator->apply($rule);
            } catch (InvalidArgumentException $e) {
                $this->addError($rule[0], $e->getMessage());
            }
        }
        if (empty($this->errors) === false) {
            throw new InvalidArgumentException($this->getErrorsAsString(), 404);
        }
    }

    protected function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    public function getErrors(): array
    {
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
        return implode(', ', $errorMessages);
    }
}
