<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$entityManager = require 'bootstrap.php';

return ConsoleRunner::createHelperSet($entityManager);
