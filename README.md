RulerZ [![Build Status](https://travis-ci.org/K-Phoen/rulerz.svg?branch=master)](https://travis-ci.org/K-Phoen/rulerz) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/K-Phoen/rulerz/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/K-Phoen/rulerz/?branch=master)
======

:warning: **This project is no longer maintained**, reach out to me if you are interested in becoming maintainer :warning:

> The central idea of Specification is to separate the statement of how to match
> a candidate, from the candidate object that it is matched against.
>
> Specifications, [explained by Eric Evans and Martin Fowler](http://www.martinfowler.com/apsupp/spec.pdf)

RulerZ is a PHP implementation of the **Specification pattern** which puts the
emphasis on three main aspects:

 * an easy and **data-agnostic [DSL](http://en.wikipedia.org/wiki/Domain-specific_language)**
   to define business rules and specifications,
 * the ability to check if a candidate **satisfies** a specification,
 * the ability to **filter or query any datasource** to only retrieve
   candidates matching a specification.


Introduction
------------

Business rules can be written as text using a dedicated language, very close to
SQL, in which case we refer to them as *rules* or they can be encapsulated in
single classes and referred to as *specifications*.

Once a rule (or a specification) is written, it can be used to check if a single
candidate satisfies it or directly to query a datasource.

The following datasources are supported natively:

 * array of arrays,
 * array of objects.

And support for each one of these is provided by an additional library:

 * Doctrine DBAL QueryBuilders: [rulerz-php/doctrine-dbal](https://github.com/rulerz-php/doctrine-dbal/),
 * Doctrine ORM QueryBuilders: [rulerz-php/doctrine-orm](https://github.com/rulerz-php/doctrine-orm/),
 * [Pomm](http://www.pomm-project.org/) models: [rulerz-php/pomm](https://github.com/rulerz-php/pomm/),
 * Elasticsearch (using the [official client](https://github.com/elasticsearch/elasticsearch-php): [rulerz-php/elasticsearch](https://github.com/rulerz-php/elasticsearch/),
 * Solr (using the [solarium](https://github.com/solariumphp/solarium): [rulerz-php/solarium](https://github.com/rulerz-php/solarium/),
 * Laravel's [Eloquent ORM](http://laravel.com/docs/5.0/eloquent): [rulerz-php/eloquent](https://github.com/rulerz-php/eloquent/).

**Killer feature:** when working with Doctrine, Pomm, or Elasticsearch, RulerZ
is able to convert rules directly in queries and does not need to fetch data
beforehand.

#### That's cool, but why do I need that?

First of all, you get to **express business rules** in a dedicated, **simple
language**.
Then, these business rules can be **encapsulated** in specification classes, reused
and composed to form more complex rules. Specifications are now **reusable** and
**testable**.
And last but not least, these rules can be used both to check if a candidate
satisfies it and to **filter any datasource**.

If you still need to be conviced, you can read the whole reasoning in [this
article](http://blog.kevingomez.fr/2015/02/07/on-taming-repository-classes-in-doctrine-among-other-things/).


Quick usage
-----------

As a quick overview, we propose to see a little example that manipulates a
simple rule and several datasources.

#### 1. Write a rule

The rule hereafter describes a "*high ranked female player*" (basically, a female
player having more than 9000 points).

```php
$highRankFemalesRule = 'gender = "F" and points > 9000';
```

#### 2. Define a datasource

We have the following datasources:

```php
// a Doctrine QueryBuilder
$playersQb = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Player', 'p');

// or an array of arrays
$playersArr = [
    ['pseudo' => 'Joe',   'gender' => 'M', 'points' => 2500],
    ['pseudo' => 'Moe',   'gender' => 'M', 'points' => 1230],
    ['pseudo' => 'Alice', 'gender' => 'F', 'points' => 9001],
];

// or an array of objects
$playersObj = [
    new Player('Joe',   'M', 40, 2500),
    new Player('Moe',   'M', 55, 1230),
    new Player('Alice', 'F', 27, 9001),
];
```

#### 3. Use a rule to query a datasource

For any of our datasource, retrieving the results is as simple as calling the
`filter` method:

```php
// converts the rule in DQL and makes a single query to the DB
$highRankFemales = $rulerz->filter($playersQb, $highRankFemalesRule);
// filters the array of arrays
$highRankFemales = $rulerz->filter($playersArr, $highRankFemalesRule);
// filters the array of objects
$highRankFemales = $rulerz->filter($playersObj, $highRankFemalesRule);
```

#### 3. (bis) Check if a candidate satisfies a rule

Given a candidate, checking if it satisfies a rule boils down to calling the
`satisfies` method:

```php
$isHighRankFemale = $rulerz->satisfies($playersObj[0], $highRankFemalesRule);
```

Going further
-------------

Check out [the documentation](doc/index.md) to discover what RulerZ can do for
you.


License
-------

This library is under the [MIT](LICENSE) license.
