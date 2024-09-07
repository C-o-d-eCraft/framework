<?php

namespace Craft\Http\Factory;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\FormRequestFactoryInterface;
use Craft\Http\Validator\AbstractFormRequest;
use Craft\Http\Factory\FormNotFoundException;

class FormRequestFactory implements FormRequestFactoryInterface
{
    public function __construct(private DIContainer $container) { }
    
    /**
     * @param string $formClassName
     * @return AbstractFormRequest
     */
    public function create(string $formClassName): AbstractFormRequest
    {
        if (class_exists($formClassName) === true) {
            return $this->container->make($formClassName);
        }

        throw new FormNotFoundException();
    }
}
