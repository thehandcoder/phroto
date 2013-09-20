<?php
session_start();

$f3 = require('../libs/f3/base.php');
$f3->set('AUTOLOAD','../classes/');
$f3->set('DEBUG',3);

$f3->route('GET /@controller/@action', 'baseGetRouter');
$f3->route('GET /@controller', 'baseGetRouter');
$f3->route('GET /', 'baseGetRouter');

$f3->route('POST /@controller/@action', 'basePostRouter');
$f3->route('POST /@controller', 'basePostRouter');
$f3->route('POST /', 'basePostRouter');

function baseGetRouter($f3, $params) {

	print_r($params);

	$controllerName = ucwords($f3->get('PARAMS.controller'));
	
	if (empty($controllerName)) {
		$controllerName = 'Hello';
	}

	$action = $f3->get('PARAMS.action');
	
	if (empty($action)) {
		$action = 'index';
	}

	$action = "get" . ucwords($action);

	$controllerName = "Controllers\\{$controllerName}";
	$controller = new $controllerName;
  $controller->$action($f3);
}

function basePostRouter($f3, $params) {
	$controllerName = ucwords($f3->get('PARAMS.controller'));
	
	if (empty($controllerName)) {
		$controllerName = 'Hello';
	}

	$action = $f3->get('PARAMS.action');
	
	if (empty($action)) {
		$action = 'index';
	}
	
	$action = "post" . ucwords($action);

	$controllerName = "Controllers\\{$controllerName}";
	$controller = new $controllerName;
  $controller->$action($f3);
}

$f3->run();