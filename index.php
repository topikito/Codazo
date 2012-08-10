<?php

define('CDZ_FW_FOLDER', '/silex-core');

require_once __DIR__ . CDZ_FW_FOLDER . '/vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app['controllers']
	->assert('id', '\d+');

$app->get('/{id}', function ($id) use ($app){
		echo 'You are at CODE '. $id;
	});

$app->get('/', function () use ($app) {
		return $app['twig']->render('paste.twig', array());
	});

$app->run();

