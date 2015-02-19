RulerZ [![Build Status](https://travis-ci.org/K-Phoen/rulerz.svg?branch=master)](https://travis-ci.org/K-Phoen/rulerz) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/K-Phoen/rulerz/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/K-Phoen/rulerz/?branch=master)
======

> The central idea of Specification is to separate the statement of how to match
> a candidate, from the candidate object that it is matched against.
>
> Specifications, [explained by Eric Evans and Martin Fowler](http://www.martinfowler.com/apsupp/spec.pdf)

RulerZ is a PHP implementation of the **Specification pattern** which puts the
emphasis on three main aspects:

 * an easy and **data-agnostic [DSL](http://en.wikipedia.org/wiki/Domain-specific_language)**
   to define business rules and specifications;
 * the ability to check if a candidate **satisfies** a specification ;
 * the ability to filter or **query any datasource** to only retrieve
   candidates matching a specification.


Table of contents
-----------------

 1. [Introduction](#introduction) and [rationale](http://blog.kevingomez.fr/2015/02/07/on-taming-repository-classes-in-doctrine-among-other-things/)
 2. [Quick usage](#quick-usage)
 3. [Installation and documentation](doc/)


Introduction
------------

Rules can be written by using a dedicated language, very close to SQL. Therefore,
they can be written by a user and saved in a database.

The rule engine used by RulerZ is [hoa/ruler](https://github.com/hoaproject/Ruler).

Currently supported target types:

 * array of arrays ;
 * array of objects ;
 * Doctrine ORM QueryBuilder.


Quick usage
-----------

### Using a rule to query a datasource

```php
// 1. Write a rule.
$rule  = 'group = "guest" and points > 30';

// 2. Define a few targets to filter data from

// a Doctrine QueryBuilder
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

// 3. And apply your rule on the targets.

$powerGuests = $rulerz->filter($usersQb, $rule);
$powerGuests = $rulerz->filter($usersArr, $rule);
$powerGuests = $rulerz->filter($usersObj, $rule);
```


Licence
-------

This library is under the
[MIT](https://github.com/K-Phoen/rulerz/blob/master/LICENSE) licence.
