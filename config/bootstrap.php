<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/reversible_uid/reversible_unique_id.php';

class Bootstrap
{
	protected $_app;
	protected $_config;

	private function __construct($config)
	{
		$this->_app = new Silex\Application();
		$this->_config = $config;
	}

	public function loadConfig()
	{
		/** CONFIG * */
		$this->_app->register(new Silex\Provider\TwigServiceProvider(), array(
			'twig.path' => __DIR__ . '/../views',
		));
		$this->_app->register(new Silex\Provider\TranslationServiceProvider(), array(
			'translator.messages' => array()
		));
		$this->_app->register(new Silex\Provider\DoctrineServiceProvider(), array(
			'db.options' => array(
				'driver' => 'pdo_mysql',
				'host' => $this->_config['database.host'],
				'dbname' => $this->_config['database.name'],
				'user' => $this->_config['database.user'],
				'password' => $this->_config['database.password'],
			),
		));
		$this->_app['debug'] = $this->_config['debug.mode'];
		return $this;
	}

	public function viewCodePage($id)
	{
		$sql = 'SELECT code FROM code WHERE unique_id = ?';
		$code = $this->_app['db']->fetchAssoc($sql, array($id));

		$viewParams = array(
			'code' => $code,
			'currentSection' => 'view'
		);

		return $this->_app['twig']->render('view.twig', $viewParams);
	}

	public function indexPage()
	{
		$this->_app->register(new Silex\Provider\FormServiceProvider);
		$form = $this->_app['form.factory']->createBuilder('form')
			->add('code', 'textarea', array('label' => ' ', 'attr' => array('style' => 'height: 300px', 'class' => 'span12')))
			->add('lang', 'text', array('label' => 'Language', 'required' => false, 'attr' => array('placeholder' => 'Auto')))
			->add('convert_tabs', 'checkbox', array('label' => 'Convert tabs to spaces', 'required' => false))
			->add('tab2spaces', 'text', array('label' => 'Tab to spaces', 'required' => false, 'attr' => array('value' => '4')))
			->getForm();

		$request = $this->_app['request'];
		if ('POST' == $request->getMethod())
		{
			$form->bindRequest($request);
			if ($form->isValid())
			{
				$this->_app['db']->beginTransaction();

				$data = $form->getData();

				$values = array(
					'code' => $data['code'],
					'created_at' => time(),
					'ip' => $_SERVER['REMOTE_ADDR'],
					'language' => $data['lang']
				);
				$insertResult = $this->_app['db']->insert('code', $values);
				$insertedId = $this->_app['db']->lastInsertId();

				$ruid = new ReversibleUniqueId();
				$uniqueId = $ruid->encode($insertedId);

				$updateResult = $this->_app['db']->update('code', array('unique_id' => $uniqueId), array('id' => $insertedId));

				$this->_app['db']->commit();

				return $this->_app->redirect('/' . $uniqueId);
			}
		}

		$viewParams = array(
			'form' => $form->createView(),
			'currentSection' => 'paste'
		);

		return $this->_app['twig']->render('paste.twig', $viewParams);
	}

	public function loadDispatcher()
	{
		$myself = $this;
		$this->_app->get('/{id}', function ($id) use ($myself) { return $myself->viewCodePage($id); } );
		$this->_app->match('/', function () use ($myself) { return $myself->indexPage(); } );
		return $this;
	}

	public function getApplication()
	{
		return $this->_app;
	}

	static public function load($config)
	{
		$me = new static($config);
		$me->loadConfig()->loadDispatcher();
		return $me->getApplication();
	}

}

$config = parse_ini_file('config.ini', true);
return Bootstrap::load($config[CDZ_ENV]);
