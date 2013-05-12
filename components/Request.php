<?php

/**
 * @class Request Component class. Contain methods, properties for wrapping HTTP request data
 * @author Maslakov Alexander <jmas.ukraine@gmail.com>
 */
class Request
{
	/**
	 * @var $_requestMethod string|null Contain current HTTP request name
	 */
	private $_requestMethod;

	/**
	 * @var $_requestMethod array Contain current request URL elements
	 */
	private $_urlElements=array();

	/**
	 * @var $_inputData array Contain current request data
	 */
	private $_inputData=array();

	public function __construct()
	{
		$this->_requestMethod = $_SERVER['REQUEST_METHOD'];

		if (isset($_SERVER['PATH_INFO'])) {
	        	$this->_urlElements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
		}
	}

	/**
	 * Get URL elements.
	 * Example:
	 * URL: http://site.com/controller/action/1/
	 * $request->getUrlElements();
	 * Result:
	 * array('controller', 'action', '1')
	 * @return array URL elements
	 */
	public function getUrlElements()
	{
		return $this->_urlElements;
	}

	/**
	 * Get information of current HTTP request method (like: GET, PUT, POST, DELETE).
	 * @return string Current HTTP request method
	 */
	public function getRequestMethod()
	{
		return $this->_requestMethod;
	}

	/**
	 * Get data that sended in current HTTP request.
	 * This method recognize current method and return it data.
	 * @return array Data of current HTTP request
	 */
	public function getData()
	{
		if (empty($this->_inputData) === true) {
			switch ($this->getRequestMethod()) {
				case 'DELETE':
				case 'PUT':
					parse_str(file_get_contents('php://input'), $data);
					$this->_inputData = $data;
					break;
				case 'POST':
					$this->_inputData = $_POST;
					break;
				default:
				case 'GET':
					$this->_inputData = $_GET;
					break;
			}
		}

		return $this->_inputData;
	}
}
