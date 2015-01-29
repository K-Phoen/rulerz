<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require './vendor/autoload.php';

$paths = ['./entities'];
$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'foo',
    'password' => 'foo',
    'dbname'   => 'foo',
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

return EntityManager::create($dbParams, $config);
