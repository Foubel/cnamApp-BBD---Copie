<?php

	use Slim\Factory\AppFactory;
	use Slim\Middleware\BodyParsingMiddleware;

	require __DIR__ . '/../vendor/autoload.php';
	
	$app = AppFactory::create();

	$app->addBodyParsingMiddleware();

	require_once __DIR__ . '/middleware.php';
	require_once __DIR__ . '/controller.php';
	require_once __DIR__ . '/route.php';

	// Run app
	$app->run();
