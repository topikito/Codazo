<?php

/**
 * Description of codazo_object
 *
 * @author robertopereznygaard
 */
abstract class CodazoObject
{
	static protected $_application;

	public static function setApplication($app)
	{
		self::$_application = $app;
	}
}
