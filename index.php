<?php

require(__DIR__ . '/vendor/autoload.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

$template = function() use ($request) {
	$pathInfo = ltrim($request->getPathInfo(), '/');
	if (empty($pathInfo)) {
		$pathInfo = 'index';
	}

	return $pathInfo . '.html.twig';
};

$loader = new Twig_Loader_Filesystem(realpath(__DIR__ . '/templates'));
$twig = new Twig_Environment($loader, array(
	'cache' => realpath(__DIR__ . '/cache'),
	'debug' => true
	)
);

$response = new Response;
try {
	$template = $twig->loadTemplate($template());
	$response->setContent($template->render(require('variables.php')));
} catch (Twig_Error_Loader $e) {
	$response->setStatusCode(404);
	$template = $twig->loadTemplate('error.html.twig');
	$response->setContent($template->render(array('title' => $e->getMessage())));
} catch (Twig_Error_Loader $e) {
	$response->setStatusCode(500);
	$template = $twig->loadTemplate('error.html.twig');
	$response->setContent($template->render(array('title' => $e->getMessage())));
}

$response->send();
