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
 * the ability to **filter or query any datasource** to only retrieve
   candidates matching a specification.


Table of contents
-----------------

 1. [Introduction](#introduction) and [rationale](http://blog.kevingomez.fr/2015/02/07/on-taming-repository-classes-in-doctrine-among-other-things/)
 2. [Quick usage](#quick-usage)
 3. [Installation and documentation](doc/index.md)


Introduction
------------

Business rules can be written as text using a dedicated language, very close to
SQL, in which case we refer to them as *rules* or they can be encapsulated in
single classes and referred to as *specifications*.

Once a rule (or a specification) is written, it can be used to check if a single
candidate satisfies it or directly to query a datasource.

The currently supported datasources are:

 * array of arrays ;
 * array of objects ;
 * Doctrine ORM QueryBuilders.

**Killer feature:** when working with QueryBuilders, RulerZ is able to convert
rules directly into DQL and does not need to fetch data beforehand.


Quick usage
-----------

As a quick overview, we propose to see a little example that manipulates a
simple rule and several datasources.

The rule described below describes what a "power guest" is (basically, a user
having more than 42 points and whose group is *guest*).

```php
$powerGuestsRule = 'group = "guest" and points > 42';
```

We have as our disposal the following datasources:

```php
// a Doctrine QueryBuilder
$usersQb = $entityManager
    ->createQueryBuilder()
    ->select('u')
    ->from('Entity\User', 'u');

// or an array of arrays
$usersArr = [
    ['name' => 'Joe', 'group' => 'guest', 'points' => 50],
    ['name' => 'Moe', 'group' => 'guest', 'points' => 25],
    ['name' => 'Al',  'group' => 'guest', 'points' => 50],
];

// or an array of objects
$usersObj = [
    new User('Joe', 'guest', 50),
    new User('Moe', 'guest', 25),
    new User('Al',  'guest', 50),
];
```

#### Using a rule to query a datasource

For any of our datasource, retrieving the *power guests* is as simple as calling
the `filter` method:

```php
$powerGuests = $rulerz->filter($usersQb, $rule);
$powerGuests = $rulerz->filter($usersArr, $rule);
$powerGuests = $rulerz->filter($usersObj, $rule);
```

#### Checking if a candidate satisfies a rule

Given a candidate, checking if it satisfies a rule boils down to calling the
`satisfies` method:

```php
$isPowerGuest = $rulerz->satisfies($usersObj[0], $rule);
```


Licence
-------

This library is under the [MIT](LICENSE) licence.
