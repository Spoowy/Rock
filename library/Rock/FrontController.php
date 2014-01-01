<?php

/**
* Class to manage most of the flow of a request to a page.
*
* @package Rock_Mvc
*/
class Rock_FrontController 
{
	public $_request;
	
	/**
	 * Startup Frontcontroller
	 */
	public function runFC()
	{
		$fc = Zend_Controller_Front::getInstance();
		
		$fc->setParam('noErrorHandler', true);
		$fc->setParam('noViewRenderer', true);
		$dispatcher = $fc->dispatch();
		
		$this->_request = $fc->getRequest();
		
		$controller = $this->dispatch();
		if (!$controller)
		{
			$controller = $this->notFound();
		}
		
		// dispatched successful
		if ($controller !== true)
		{
			print $controller;
		}
	}
	
	/**
	 * Dispatcher: From Request to Controller instance
	 * 
	 * @return controller|false Controller instance or false if not found
	 */
	public function dispatch()
	{
		$request = array(
			'ControllerName' => strtolower($this->_request->getControllerName()),
			'ActionName' => strtolower($this->_request->getActionName()) . 'Action'
		);
		
		// register
		$this->_request->setControllerName($request['ControllerName']);
		$this->_request->setActionName($request['ActionName']);
		
		// get it from database
		$routePrefix = $this->route($request['ControllerName']);
		
		if (!$routePrefix)
		{
			// Nothing found in database
			return ;
		}
		
		// you can do some login and visitor checking here & changing the route when neccessary
		
		// does the file (got by db) actually exist?
		if (class_exists($routePrefix['route_prefix']))
		{
			// yey! it does. get controller instance
			$controller = new $routePrefix['route_prefix']($this->_request, new Zend_Controller_Response_Http());
			
			// method too?
			if (method_exists($controller, $request['ActionName']))
			{
				// OK! got something here. get it out there!
				return $controllerResponse = $controller->{$request['ActionName']}();
			} 
		}
		
		// Nothing there at all
		return ;
	}
	
	/**
	 * Function that sets the not found error controllers
	 * 
	 * @return Error Controller instance with action
	 */
	public function notFound()
	{
		$routePrefix = $this->route('404');
		if (!$routePrefix)
		{
			// could not even find the 404-class.
			exit;
		}
		$errorController = new $routePrefix['route_prefix']; 
		return $errorController->{'indexAction'}(); // default action for 404 page
	}
	
	/**
	 * Get Route Class
	 * 
	 * @param string $controllerName
	 */
	public function route($controllerName)
	{
		return Rock_Model::create('Rock_Model_Router')->getRouteClassFromPrefix($controllerName);
	}
}