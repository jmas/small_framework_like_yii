<?php

// Base directory path of application
defined('BASE_DIR')           or define('BASE_DIR', dirname(__FILE__));

// Directroy path of controllers files
defined('CONTROLLERS_DIR')    or define('CONTROLLERS_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'controllers');


/**
 * App is main class that contain bootstrap functionality,
 * store application config and have factory for components.
 *
 * @class App Main class
 * @author Maslakov Alexander <jmas.ukraine@gmail.com>
 */
class App
{
	/**
	 * @var $defaultController string Default controller name
	 */
	public $defaultController = 'index';

	/**
	 * @var $defaultAction string Default controller action name
	 */
	public $defaultAction = 'get';

	/**
	 * @var $componentsConfig array Components configuration data
	 */
	public $componentsConfig = array();

	/**
	 * @var $import array List of import directories for autoloading
	 */
	public $import = array(
		'components',
		'models',
	);

	/**
	 * @var $_instance App Instance of this class
	 */
	protected static $_instance;

	/**
	 * @var $_components List of loaded components instances
	 */
	protected $_components = array();

	/**
	 * @var $_controller Current loaded controller instance
	 */
	protected $_controller;


	/**
	 * Use this method for getting instance of App class.
	 * @return App Instance of this class
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Run current application.
	 * This method take request data like URL elements and create a new
	 * instance of controller. If controller not present in URL - we take
	 * controller name from $this->defaultCOntroller property.
	 * Then we call controller action/method equialent request method (post, get, ...).
	 * If method not present - we call default action.
	 * @throw Exception
	 * @return null
	 */
	public function run()
	{
		$urlElements = $this->request->getUrlElements();
		$method = $this->request->getRequestMethod();
		
		if (isset($urlElements[0]) && empty($urlElements[0]) === false) {
			$controllerClass = ucfirst($urlElements[0]) . 'Controller';
		} else {
			$controllerClass = ucfirst($this->defaultController) . 'Controller';
		}

		$filePath = CONTROLLERS_DIR . DIRECTORY_SEPARATOR . $controllerClass . '.php';

		if (file_exists($filePath)) {
			require_once($filePath);
		} else {
			throw new Exception("Controller file with name '{$controllerClass}' not found.");
		}

		$this->_controller = new $controllerClass;

		$controllerMethod = strtolower($method);

		if (method_exists($this->_controller, $controllerMethod) === false) {
			$controllerMethod = $this->defaultAction;
		} 

		if (method_exists($this->_controller, $controllerMethod) === false) {
			throw new Exception("Controller action (method) '{$controllerMethod}' not found.");
		}

		call_user_func_array(
			array($this->_controller, $controllerMethod), // controller method
			array_slice($urlElements, 1) // method params
		);
	}

	/**
	 * Set application configuration.
	 * This method receive array of configuration properties.
	 * Then this method call applyConfig.
	 * @param $config array Configuration array
	 * @return null
	 */
	public function setConfig(array $config)
	{
		$this->applyConfig($this, $config);
	}

	/**
	 * Magic method __get.
	 * Use this method for quick load components class and get instance.
	 * For each component we load configuration properties that stored at $this->componentsConfig property.
	 * Example:
	 * $db = App::getInstace()->Db;
	 * We get configurated db component.
	 * @param $componentName string Component name from configuration file
	 * @return object Component object
	 */
	public function __get($componentName)
	{
		if (isset($this->_components[$componentName]) === true) {
			return $this->_components[$componentName];
		}

		if (isset($this->componentsConfig[$componentName])) {
			$className = $this->componentsConfig[$componentName]['class'];
			$component = new $className;
			$this->applyConfig($component, $this->componentsConfig[$componentName]);
			$this->_components[$componentName] = $component;
		} else {
			throw new Exception("Component '{$componentName}' not set in configuration file. Sorry.");
		}

		return $this->_components[$componentName];
	}

	/**
	 * Method for wrap php errors.
	 * @throw Exception
	 * @return null
	 */
	public static function captureErrorHandler($number, $message, $file, $line)
	{
		throw new Exception("PHP ERROR. Number: {$number}; Message: {$message}; File: {$file}; Line: {$line}.");
	}

	/**
	 * Special protected method that used for autoloading.
	 * @param $className string Class name for load
	 * @throw Exception
	 * @return null
	 */
	private function loadFile($className)
	{
		$founded = false;

		foreach ($this->import as $dirName) {
			$filePath = str_replace('.', DIRECTORY_SEPARATOR, $dirName) . DIRECTORY_SEPARATOR . $className . '.php';

			if (file_exists($filePath)) {
				require_once($filePath);
				$founded = true;
			}
		}

		if ($founded === false) {
			throw new Exception("Component with class name '{$className}' not found.");
		}
	}

	/**
	 * Special protected method that apply configuration properties
	 * to instance.
	 * @param $instance object|App Instance of App or component object
	 * @param $config array Configuration properties array
	 * @return null
	 */
	private function applyConfig($instance, array $config)
	{
		foreach ($config as $key=>$value) {
			$instance->{$key} = $value;
		}
	}

	/**
	 * Protected constructor means that this class is singlton.
	 * Register autoloading. Register capture php errors.
	 */
	protected function __construct()
	{
		spl_autoload_register(array($this, 'loadFile'), true);
		set_error_handler(array('App', 'captureErrorHandler'));
	}

	/**
	 * Protected magic method clone means that this class is singlton.
	 */
	protected function __clone() { }
}
