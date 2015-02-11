Writing rules
=============

This guide will show you how to instantiate the engine and how to write rules.

## Context

In the following examples, we'll define rules to work with this collection:

```php
$users = [
    ['name' => 'Joe', 'group'    => 'guest', 'points' => 40],
    ['name' => 'Moe', 'customer' => 'guest', 'points' => 20],
    ['name' => 'Al',  'group'    => 'admin', 'points' => 60],
];
```

## Step 1: Instanciate the RulerZ engine

In order to work, the `RulerZ` engine needs an interpreter and at least one
executor.
The interpreter will parse your rule while the executors will be in charge of
using the parsed rules (to filter target or to check if a target satisfies the
rule for instance).
An executor handles a specific type of target (an array, a QueryBuilder, ...),
that's why you can have several executors registered into the same RulerZ engine.

Enough said, here is the code:

```php
use RulerZ\RulerZ;
use RulerZ\Executor;
use RulerZ\Interpreter\HoaInterpreter;

$rulerz = new RulerZ(
    new HoaInterpreter(), [
        new Executor\ArrayExecutor(),
        new Executor\DoctrineQueryBuilderExecutor(),
    ]
);
```

`$rulerz` now contains a RulerZ engine able to filter both arrays and Doctrine
ORM QueryBuilders.

## Step 2: Write a rule

As a quick overview of the syntax, we'll see a few examples showcasing the main
possibilities of the language.

### Simple comparisons

```php
$rule  = 'group in :groups and points > :min_points';
```
In this example, we express two filters: the first on the `group` and another one
on the `points`. Each one of these attributes is compared to a parameter which
will be defined later, hence the `:my_parameter` syntax.

The following operators are supported by default by all executors: `and`, `or`,
`=`, `!=`, `>`, `>=`, `<`, `<=`.

### Function calls

If the filter that you are trying to express needs a function, you can use the
syntax shown in the next example to write your rule:

```php
$rule  = 'group in :groups and points > :min_points and length(name) > 3';
```
**N.B**: while the Doctrine ORM executor supports all function available in the
DQL language, the other executors require that you define the functions you want
to use. See the "[Custom operators](custom_operators.md)" section.

## That was it!

Now that you can write rules, you can use them to [filter targets](filter.md)
or to [check if a target satisfies them](satisfies.md).

[Return to the index to explore the other possibilities of the library](index.md)
