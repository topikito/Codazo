<?php

function codazoAutoloader($className)
{
	$extensions = array(".php");
	$paths = array('controllers', 'models', 'core','controllers/apis');
	$className = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));
	foreach ($paths as $path)
	{
		$path = dirname(__DIR__) . DIRECTORY_SEPARATOR .'src' . DIRECTORY_SEPARATOR . $path;
		$filename = $path . DIRECTORY_SEPARATOR . $className;
		foreach ($extensions as $ext)
		{
			if (is_readable($filename . $ext))
			{
				require_once $filename . $ext;
				break;
			}
		}
	}
}

spl_autoload_register('codazoAutoloader');