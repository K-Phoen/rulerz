<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

list($em, $_) = require_once './examples/bootstrap/bootstrap_doctrine.php';

return ConsoleRunner::createHelperSet($em);
