<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of codazo
 *
 * @author robertopereznygaard
 */
class CodazoController extends CodazoObject
{

	protected   $_app;
    protected   $_config;

    protected   $_typeOfView = 'twig';

	public function __construct()
	{
		$this->_app     = self::$_application;
        $this->_config    = self::$_configuration;
	}

    /**
     * Checks if the params are not empty for vital usages
     *
     * @return bool
     */
    protected function _checkVitalParams()
    {
        $params = func_get_args();
        foreach ($params as $param)
        {
            if (empty($param))
            {
                return false;
            }
        }
        return true;
    }

    protected function _render($templateName = null, $args = null)
    {
        switch ($this->_typeOfView)
        {
            case 'json':
                return $this->_app->json($args);
                break;

            case 'twig':
            default:
                return $this->_app['twig']->render($templateName, $args);
                break;
        }
    }

    protected function _renderError($message, $errorCode = 500)
    {
        switch ($this->_typeOfView)
        {
            case 'json':
                return $this->_app->json($message, $errorCode);
                break;

            case 'twig':
            default:
                //Is this really necessary?
                break;
        }
    }

    public function setTypeOfView($type)
    {
        $this->_typeOfView = $type;
        return true;
    }

}

?>
