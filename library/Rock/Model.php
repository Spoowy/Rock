<?php

/**
* Models
*
* @package Rock_Mvc
*/
abstract class Rock_Model
{
	/**
	* Database object
	*
	* @var Zend_Db_Adapter_Abstract
	*/
	protected $_db = null;

	/**
	* Constructor.
	*/
	public function __construct()
	{
	}

	/**
	* Helper method to get the database object.
	*
	* @return Zend_Db_Adapter_Abstract
	*/
	protected function _getDb()
	{
		if ($this->_db === null)
		{
			$this->_db = Rock_Model::loadDb(); // WTF?!
		}

		return $this->_db;
	}
	
	/**
	 * Load the database object.
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function loadDb() // there is a better way to do this
	{
		$db = Zend_Db::factory('mysqli',
				array(
						'host' => 'localhost',
						'port' => '3306',
						'username' => 'root',
						'password' => '',
						'dbname' => 'rock',
						'adapterNamespace' => 'Zend_Db_Adapter',
						'charset' => 'utf8'
				)
		);
		return $db;
	}
	
	/**
	* this method gets the model
	*
	* @param string Class to load
	*
	* @return Rock_Model
	*/
	public static function create($class)
	{
		$createClass = Rock_Model::loadClass($class);
		if (!$createClass)
		{
			throw new Exception("Invalid model '$class' specified");
		}

		return new $createClass;
	}
	
	/**
	* checks whether class is autoloadable
	*
	* @param string $class Name of class
	*
	* @return false|string False or name of class to instantiate
	*/
	public static function loadClass($class)
	{
		if (!Rock_Autoloader::getInstance()->autoload($class))
		{
			return false;
		}
		$class;
		
		return $class;
	}
}