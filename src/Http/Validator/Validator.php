<?php

namespace Craft\Http\Validator;

use InvalidArgumentException;
use Craft\Http\Validator\Rules\ValidationRuleInterface;

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
            $ruleName();
            return;
        }

        $ruleClass = $this->getRuleClass($ruleName);
        if ($ruleClass === null) {
            throw new InvalidArgumentException("Правило валидации {$ruleName} не существует.");
        }

        $this->validateAttributes($attributes, $ruleClass, $params);
    }

    protected function getRuleClass(string $ruleName): ?ValidationRuleInterface
    {
        $rulesNamespace = 'Craft\\Http\\Validator\\Rules\\';
        $className = $rulesNamespace . ucfirst($ruleName) . 'Rule';
        return class_exists($className) ? new $className() : null;
    }

    protected function validateAttributes(array|string $attributes, ValidationRuleInterface $ruleClass, array $params): void
    {
        if (is_array($attributes) === true) {
            foreach ($attributes as $attribute) {
                $ruleClass->validate($attribute, $this->data[$attribute] ?? null, $params, $this);
            }
        }
        if (is_array($attributes) === false) {
            $ruleClass->validate($attributes, $this->data[$attributes] ?? null, $params, $this);
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
