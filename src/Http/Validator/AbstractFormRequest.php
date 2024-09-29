<?php

namespace Craft\Http\Validator;

use Craft\Http\Exceptions\BadRequestHttpException;

abstract class AbstractFormRequest
{
    public array $formData;
    public array $responseData = [];
    public array $errors = [];
    protected Validator $validator;
    private array $skipEmptyRuleValues = [];
    private bool $isSkipEmptyValues = false;

    public function __construct()
    {
        $this->validator = new Validator($this->formData);
    }

    /**
     * @return array
     */
    abstract public function rules(): array;

    /**
     * @return void
     * @throws BadRequestHttpException
     */
    public function validate(): void
    {
        foreach ($this->rules() as $rule) {
            $attributes = (array)$rule[0];

            if ($this->isSkipEmptyValues === true && $this->shouldSkipRule($attributes) === true) {
                continue;
            }

            try {
                $validator = new Validator($this->formData);

                $this->validator->apply($rule);
            } catch (BadRequestHttpException $e) {
                $this->addError($rule[0], $e->getMessage());
            }
        }

        if (empty($this->getErrors()) === false) {
            throw new BadRequestHttpException($this->getErrorsAsString());
        }
    }

    /**
     * @param string $attribute
     * @param string $message
     * @return void
     */
    protected function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        $this->errors = array_merge($this->errors, $this->validator->errors);

        return $this->errors;
    }

    /**
     * @return string
     */
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

    /**
     * @return void
     */
    public function setSkipEmptyValues(): void
    {
        $this->isSkipEmptyValues = true;
        $allKeys = [];

        foreach ($this->rules() as $rule) {
            $fields = (array)$rule[0];
            $allKeys = array_merge($allKeys, $fields);
        }

        $uniqueKeys = array_unique($allKeys);

        foreach ($uniqueKeys as $field) {
            if (array_key_exists($field, $this->formData) === false) {
                $this->skipEmptyRuleValues[$field] = true;
            }
        }
    }

    /**
     * @param array $attributes
     * @return bool
     */
    private function shouldSkipRule(array $attributes): bool
    {
        foreach ($attributes as $attribute) {
            if (isset($this->skipEmptyRuleValues[$attribute]) === true) {
                return true;
            }
        }

        return false;
    }
}
