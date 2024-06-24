<?php

namespace Craft\Http\Validator;


use Craft\Contracts\RequestInterface;
use Craft\Http\Exceptions\HttpException;
use Exception;
use InvalidArgumentException;

abstract class AbstractFormRequest
{
    /**
     * Данные запроса.
     *
     * @var array
     */
    public array $data;

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
        $this->data = $this->request->getQueryParams() ?? $this->request->getBodyContents();

        if (in_array($this->request->getMethod(), ["POST", "DELETE"]) === true) {
            $this->data = $this->request->getBodyContents() ?? $this->request->getQueryParams();
        }

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
     *
     * @return void
     */
    public function getErrorsToString($allErrorsString = ''): void
    {
        foreach ($this->getErrors() as $error) {
            foreach ($error as $errorText) {
                throw new HttpException($errorText);
            }
        }
    }
}
