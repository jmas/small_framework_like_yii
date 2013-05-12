<?php

/**
 * @class Db Component class. Decorator for PDO
 * @author Maslakov Alexander <jmas.ukraine@gmail.com>
 */
class DbPDO
{
	/**
	 * @var $_db PDO instance
	 */
	private $_db;

	/**
	 * @var string The Data Source Name, or DSN, contains the information required to connect to the database.
	 */
	public $dsn = 'mysql:dbname=app;host=127.0.0.1;charset=utf8';

	/**
	 * @var string Database user
	 */
	public $user = 'root';

	/**
	 * @var string Database password
	 */
	public $password = '';

	/**
	 * @var string Database driver options
	 */
	public $driverOptions = array();


	/**
	 * Margic method for wrapping PDO methods.
	 * Make first call for initialize DPO object.
	 * @param $method string Called method
	 * @param $params array Array of arguments submitted with call
	 * @return mixed Method result data
	 */
	public function __call($method, array $params=array())
	{
		if ($this->_db === null) {
			$this->_db = new PDO(
				$this->dsn,
				$this->user,
				$this->password,
				$this->driverOptions
			);
		}
		
		return call_user_func_array(array($this->_db, $method), $params);
	}
}
