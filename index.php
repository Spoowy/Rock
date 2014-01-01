<?php

$fileDir = dirname(__FILE__);

// Setup Autoloader
require($fileDir . '/library/Rock/Autoloader.php');
Rock_Autoloader::getInstance()->setupAutoloader($fileDir . '/library');

$fc = new Rock_FrontController();
$fc->runFC();