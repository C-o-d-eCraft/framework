<?php

namespace Craft\Http\Validator;


use Craft\Contracts\RequestInterface;
use Exception;
use InvalidArgumentException;

abstract class AbstractFormRequest
{
    /**
     * Данные запроса.
     *
     * @var array
     */
    protected array $data;

    /**
     * Данные запроса.
     *
     * @var string
     */
    protected string $allErrorsString;

    /**
     * @var Validator
     */
    protected Validator $validator;

    /**
     * Конструктор класса.
     *
     * @param RequestInterface $request Данные объекта Request.
     */
    public function __construct(readonly private RequestInterface $request)
    {
        $this->data = $this->request->getBodyContents();
        $this->validator = new Validator($this->rules());
    }

    /**
     * Возвращает правила валидации для формы.
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Валидирует данные запроса.
     *
     * @return bool
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function validate(): bool
    {
        if($this->validator->validate($this->data) === false){
            throw new InvalidArgumentException($this->getErrorsToString());
        }

        return true;
    }

    /**
     * Возвращает ошибки валидации.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->validator->getErrors();
    }

    /**
     * Возвращает ошибки валидации, строкой.
     * @return string
     */
    public function getErrorsToString($allErrorsString = ''): string
    {
        foreach ($this->getErrors() as $error) {
            foreach ($error as $errorText) {
                $allErrorsString .= $errorText . ', ';
            }
        }

        return json_decode($allErrorsString);
    }
}
