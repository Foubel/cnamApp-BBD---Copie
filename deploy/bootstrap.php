<?php
	use Doctrine\ORM\Tools\Setup;
	use Doctrine\ORM\EntityManager;
	date_default_timezone_set('America/Lima');
	require_once "vendor/autoload.php";
	$isDevMode = true;
	$config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/config/yaml"), $isDevMode);
	$conn = array(
	'host' => 'dpg-cm23hh7qd2ns73d8dua0-a.oregon-postgres.render.com',

	'driver' => 'pdo_pgsql',
	'user' => 'cnam_db_mn9y_user',
	'password' => 'VHwiSVl5rD53vVefGgirRHoPxVWmN7X5',
	'dbname' => 'cnam_db_mn9y',
	'port' => '5432'
	);


	$entityManager = EntityManager::create($conn, $config);



