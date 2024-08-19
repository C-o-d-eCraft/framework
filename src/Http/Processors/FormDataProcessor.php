<?php

namespace Craft\Http\Processors;

class FormDataProcessor
{
    /**
     * @param array $params
     * @return array
     */
    public function processFormData(array $params): array
    {
        if (isset($params['formData']) && $params['formData'] instanceof \stdClass) {
            $formDataArray = json_decode(json_encode($params['formData']), true);
            $params['formData'] = $formDataArray;

            $params = array_merge($params, $formDataArray);
        }

        return $params;
    }
}
