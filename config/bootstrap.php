<?php

require_once __DIR__ . '/../silex-core/vendor/autoload.php';

$app = new Silex\Application();

/** CONFIG * */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/../views',
));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
	'translator.messages' => array()
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.options' => array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'dbname' => 'codazo_db',
		'user' => 'root',
		'password' => 'root',
	),
));
$app['debug'] = true;


/** DISPATCHER * */
$app->get('/{id}', function ($id) use ($app)
	{
	var_dump($id);die;
		$sql  = "SELECT code FROM code WHERE unique_id = ?";
		$code = $app['db']->fetchAssoc($sql, array((int) $id));

		$viewParams = array(
			'code'				=> $code,
			'currentSection'	=> 'view'
		);

		return $app['twig']->render('view.twig', $viewParams);
	});

$app->match('/', function () use ($app)
	{
		$app->register(new Silex\Provider\FormServiceProvider);
		$form = $app['form.factory']->createBuilder('form')
			->add('code', 'textarea', array('label' => ' ', 'attr' => array('style' => 'height: 300px', 'class' => 'span12')))
			->add('lang', 'text', array('label' => 'Language', 'required' => false, 'attr' => array('placeholder' => 'Auto')))
			->add('convert_tabs', 'checkbox', array('label' => 'Convert tabs to spaces', 'required' => false))
			->add('tab2spaces', 'text', array('label' => 'Tab to spaces', 'required' => false, 'attr' => array('value' => '4')))
			->getForm();

		$request = $app['request'];
		if ('POST' == $request->getMethod())
		{
			$form->bindRequest($request);
			if ($form->isValid())
			{
				$data = $form->getData();

				var_dump($data);


				// do something with the data
				// redirect somewhere
				//return $app->redirect('...');
				die('---');
			}
		}

		$viewParams = array(
			'form'				=> $form->createView(),
			'currentSection'	=> 'paste'
		);

		return $app['twig']->render('paste.twig', $viewParams);
	});

return $app;
