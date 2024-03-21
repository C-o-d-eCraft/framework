<?php

namespace Craft\Contracts;

interface ConnectionFactoryInterface
{
    /**
     * @param array $config
     * @return DataBaseConnectionInterface
     */
    public function createConnection(array $config): DataBaseConnectionInterface;
}
