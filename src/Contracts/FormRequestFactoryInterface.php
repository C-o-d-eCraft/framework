<?php

namespace Craft\Contracts;

use Craft\Http\Validator\AbstractFormRequest;

interface FormRequestFactoryInterface
{
    /**
     * @param string $formClassName
     * @return AbstractFormRequest
     */
    public function create(string $formClassName): AbstractFormRequest;
}
