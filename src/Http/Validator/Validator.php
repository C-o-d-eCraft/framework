<?php

namespace Craft\Http\Validator;

use Craft\Contracts\ValidationRuleInterface;
use InvalidArgumentException;

class Validator
{
    public array $errors = [];
    public array $data;

    protected array $messages = [
        'required' => 'Поле :attribute обязательно для заполнения.',
        'string' => 'Поле :attribute должно быть строкой.',
        'integer' => 'Поле :attribute должно быть целым числом.',
        'numeric' => 'Поле :attribute должно быть числом.',
        'inArray' => 'Поле :attribute не существует.',
        'notInArray' => 'Поле :attribute уже существует.',
        'email' => 'Поле :attribute должно быть адресом электронной почты.',
        'boolean' => 'Поле :attribute должно быть истинным или ложным.',
        'date' => 'Поле :attribute должно быть действительной датой.',
    ];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function apply(array $rule): void
    {
        [$attributes, $ruleName, $params] = array_pad($rule, 3, []);

        if (is_callable($ruleName)) {
            if (is_array($attributes) === false) {
                $attributes = [$attributes];
            }

            foreach ($attributes as $attr) {
                $ruleName($this->data[$attr] ?? null, $attr);
            }

            return;
        }

        $ruleClass = $this->getRuleClass($ruleName);

        $this->validateAttributes($attributes, $ruleClass, $params);
    }

    protected function getRuleClass(string $ruleName): ValidationRuleInterface
    {
        $rulesNamespace = 'Craft\\Http\\Validator\\Rules\\';
        $className = $rulesNamespace . ucfirst($ruleName) . 'Rule';

        if (class_exists($className) === true) {
            return new $className();
        }

        throw new InvalidArgumentException("Правило валидации {$ruleName} не существует.");
    }

    protected function validateAttributes(array|string $attributes, ValidationRuleInterface $ruleClass, array $params): void
    {
        if (is_array($attributes) === false) {
            $attributes = [$attributes];
        }

        foreach ($attributes as $attr) {
            $ruleClass->validate($attr, $this->data[$attr] ?? null, $params, $this);
        }
    }

    public function addError(string $attribute, string $rule, array $params = []): void
    {
        $message = $this->messages[$rule] ?? 'Ошибка валидации';
        $message = str_replace(':attribute', $attribute, $message);

        foreach ($params as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }

        $this->errors[$attribute][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
