<?php

/**
 * Autoloader Class
 * 
 * @package Rock_Core
 */
class Rock_Autoloader
{
	/**
	 * Instance holder
	 * 
	 * @var Rock_Autoloader
	 */
	private static $_instance;
	
	/**
	 * Path to the application's library
	 * 
	 * @var string
	 */
	protected $_rootDir = '.';
	
	/**
	 * Stores setup state
	 * 
	 * @var boolean
	 */
	protected $_setup = false;
	
	/**
	 * Setup the autoloader. 
	 * 
	 * @param string $rootDir(path to the application directory)
	 */
	public function setupAutoloader($rootDir)
	{
		if ($this->_setup)
		{
			return ;
		}
		
		$this->_rootDir = $rootDir;
		$this->_setupAutoloader();
		$this->_setup = true;
	}
	
	/**
	 * applies autoloader
	 */
	protected function _setupAutoloader()
	{
		if (@ini_get('open_basedir'))
		{
			set_include_path($this->_rootDir . PATH_SEPARATOR . '.');
		}
		else
		{
			set_include_path($this->_rootDir . PATH_SEPARATOR . '.' .
					PATH_SEPARATOR . get_include_path());
		}
	
		spl_autoload_register(array($this, 'autoload'));
	}
	
	/**
	* Autoload the specified class.
	*
	* @param string $class Name of class to autoload
	*
	* @return boolean
	*/
	public function autoload($class)
	{
		if (class_exists($class, false) || interface_exists($class, false))
		{
			return true;
		}

		if ($class == 'utf8_entity_decoder')
		{
			return true;
		}

		$filename = $this->autoloaderClassToFile($class);
		if (!$filename)
		{
			return false;
		}

		if (file_exists($filename))
		{
			include($filename);
			return (class_exists($class, false) || interface_exists($class, 
				false));
		}
		return false;
	}
	
	/**
	* Resolves a class name to an autoload path.
	*
	* @param string Name of class to autoload
	*
	* @return string|false False if the class contains invalid characters.
	*/
	public function autoloaderClassToFile($class)
	{
		if (preg_match('#[^a-zA-Z0-9_]#', $class))
		{
			return false;
		}

		return $this->_rootDir . '/' . str_replace('_', '/', $class) . '.php';
	}
	
	/**
	 * Gets the autoloader's root directory.
	 *
	 * @return string
	 */
	public function getRootDir()
	{
		return $this->_rootDir;
	}

	/**
	* Gets the autoloader instance.
	*
	* @return Rock_Autoloader
	*/
	public static final function getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}