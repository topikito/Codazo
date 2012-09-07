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

	protected $_app;

	public function __construct()
	{
		$this->_app = self::$_application;
	}

}

?>
