<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require './vendor/autoload.php';

$paths = ['./entities'];
$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'joblist',
    'password' => 'joblist',
    'dbname'   => 'joblist',
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

return EntityManager::create($dbParams, $config);
