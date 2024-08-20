<?php

namespace Craft\Http\Processors;

class FormDataProcessor
{
    /**
     * @param array $headers
     * @param array $params
     * @return array
     */
    public function processFormData(array $headers, array $params): array
    {
        if (isset($headers['X-BASE-AUTH']) === true && $headers['X-BASE-AUTH'] !== null) {
            $params['token'] = $headers['X-BASE-AUTH'];
        }

        return $this->processData($params);
    }
    
    /**
     * @param array $params
     * @return array
     */
    private function processData(array $params): array
    {
        if (isset($params['formData']) && $params['formData'] instanceof \stdClass) {
            $formDataArray = json_decode(json_encode($params['formData']), true);
            $params['formData'] = $formDataArray;

            $params = array_merge($params, $formDataArray);
        }

        return $params;
    }
}
