<?php

class CodeApiController extends CodeController
{

    public function saveCode()
    {
        $request = $this->_app['request'];
        $codeValue = $request->get('code');
        if (!$this->_checkVitalParams($codeValue))
        {
            $this->_renderError('Expected "code" but not found.');
        }

        $data = array('code' => $codeValue);

        $tryToCatch = array('lang', 'line');
        foreach ($tryToCatch as $field)
        {
            $value = $request->get($field);
            if (isset($value))
            {
                $data[$field] = $value;
            }
        }

        $uniqueId   = $this->_saveCodeModel($data);
        $viewParams = array(
            'url' => 'http://' . $this->_config['codazo.hostname'] . '/' . $uniqueId,
            'uid' => $uniqueId
        );

        return $this->_render(null, $viewParams);
    }

}