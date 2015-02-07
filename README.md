RulerZ [![Build Status](https://travis-ci.org/K-Phoen/rulerz.svg?branch=master)](https://travis-ci.org/K-Phoen/rulerz) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/K-Phoen/rulerz/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/K-Phoen/rulerz/?branch=master)
======

This library allows to filter multiple types of targets using the same rule
engine.

Rules can be written by using a dedicated language, very close to SQL. Therefore,
they can be written by a user and saved in a database.

The rule engine used by RulerZ is [hoa/ruler](https://github.com/hoaproject/Ruler).

Currently supported target types:

 * array of arrays ;
 * array of objects ;
 * Doctrine ORM QueryBuilder.

Installation
------------

```
composer require 'kphoen/rulerz'
```

Usage
-----

```php
$rulerz = new \RulerZ\RulerZ(
    new \RulerZ\Interpreter\HoaInterpreter(), [
        new \RulerZ\Executor\ArrayExecutor([
            'length' => 'strlen',
        ]),
        new \RulerZ\Executor\DoctrineQueryBuilderExecutor(),
    ]
);

// 1. Write a rule.
$rule  = 'group in :groups and points > :points and length(name) > 2';

// 2. Filter a collection
$usersQb = $entityManager
    ->createQueryBuilder()
    ->select('u')
    ->from('Entity\User', 'u');

// or an array of arrays
$usersArr = [
    ['name' => 'Joe', 'group' => 'guest', 'points' => 40],
    ['name' => 'Moe', 'group' => 'guest', 'points' => 20],
    ['name' => 'Al',  'group' => 'guest', 'points' => 40],
];

// or an array of objects
$usersObj = [
    new User('Joe', 'guest', 40),
    new User('Moe', 'guest', 20),
    new User('Al',  'guest', 40),
];

// 3. Enjoy!
$parameters = array(
    'points' => 30,
    'groups' => ['customer', 'guest'],
);

var_dump($rulerz->filter($usersQb, $rule, $parameters));
var_dump($rulerz->filter($usersArr, $rule, $parameters));
var_dump($rulerz->filter($usersObj, $rule, $parameters));
```

Documentation
-------------

The documentation can be found in the `doc` directory. Have a look at the
[index.md](doc/index.md) if you don't know what you are looking for.

Licence
-------

This library is under the
[MIT](https://github.com/K-Phoen/rulerz/blob/master/LICENSE) licence.
