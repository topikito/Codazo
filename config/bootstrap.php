<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/lib/reversible_uid/reversible_unique_id.php';
require_once __DIR__ . '/codazo_autoload.php';

class Bootstrap
{

	protected $_config;
	protected $_app;

	private function __construct($config)
	{
		$this->_app = new Silex\Application();
		$this->_config = $config;
	}

	public function loadConfig()
	{
		/** CONFIG * */
		$this->_app->register(new Silex\Provider\TwigServiceProvider(), array(
			'twig.path' => __DIR__ . '/../src/views',
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

    private function loadDispatcher()
    {
        CodazoObject::setApplication($this->_app);
        CodazoObject::setConfig($this->_config);

        list($firstName) = explode('.',$_SERVER['HTTP_HOST']);

        switch ($firstName)
        {
            case $this->_config['codazo.api.subdomain']:
                $this->loadApiDispatcher();
                break;

            default:
                $this->loadDefaultDispatcher();
        }

        $this->_app->match('/{whatever}', function() { return CodazoController::defaultJSONError(); });

        return $this;
    }

    public function loadApiDispatcher()
    {
        $this->_app->post('/save.{typeOfView}', function ($typeOfView)
        {
            $con = new CodeApiController(array('callingFrom' => 'api', 'typeOfView' => $typeOfView));
            return $con->saveCode();
        });

        $this->_app->get('/view/{id}.{typeOfView}', function ($id, $typeOfView)
        {
            $con = new CodeApiController(array('callingFrom' => 'api', 'typeOfView' => $typeOfView));
            return $con->viewCode($id);
        });

        return $this;
    }

	public function loadDefaultDispatcher()
	{
        /** POSTERS **/
        $this->_app->post('/', function ()
        {
            $con = new CodeController();
            return $con->saveCode();
        });

        /** GETTERS **/
		$this->_app->get('/', function ()
			{
				$con = new CodeController();
				return $con->index();
			});

		$this->_app->get('/{id}', function ($id)
			{
				$con = new CodeController();
				return $con->viewCode($id);
			});

		$this->_app->get('/{id}/raw', function ($id)
			{
				$con = new CodeController();
				return $con->viewRaw($id);
			});

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
define('ENV', $config['codazo.env']);
return Bootstrap::load($config[ENV]);
