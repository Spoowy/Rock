<?php

class Rock_Model_Router extends Rock_Model
{
	public function getRouteClassFromPrefix($prefix)
	{
		return $this->_getDb()->fetchRow('
			SELECT *
			FROM route_prefix
			WHERE original_prefix = ?
		', $prefix);
	}
}