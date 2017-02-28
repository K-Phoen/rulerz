Writing rules
=============

This guide will show you how to instantiate the engine and how to write rules.

## Context

In the following examples, we'll define rules to work with this collection:

```php
$players = [
    ['pseudo' => 'Joe',   'fullname' => 'Joe la frite',             'gender' => 'M', 'points' => 2500],
    ['pseudo' => 'Moe',   'fullname' => 'Moe, from the bar!',       'gender' => 'M', 'points' => 1230],
    ['pseudo' => 'Alice', 'fullname' => 'Alice, from... you know.', 'gender' => 'F', 'points' => 9001],
];
```

## Step 1: Instantiate the RulerZ engine

In order to work, the `RulerZ` engine needs a compiler and at least one
compilation target.
The compiler is responsible for the construction of an executor, in charge of
handling the rule (to filter target or to check if a target satisfies the
rule for instance).
An executor handles a specific type of target (an array, a QueryBuilder, ...),
that's why you can have several compilation targets registered in the same
RulerZ engine.

Enough said, here is the code:

```php
use RulerZ\Compiler\Compiler;
use RulerZ\Compiler\Target;
use RulerZ\RulerZ;

// compiler
$compiler = Compiler::create();

// RulerZ engine
$rulerz = new RulerZ(
    $compiler, [
        new Target\Sql\DoctrineQueryBuilder(),
        new Target\Native([
            'length' => 'strlen'
        ]),
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
DQL language, the other compilation targets require that you define the functions
you want to use. See the "[Custom operators](custom_operators.md)" section.

## That was it!

Now that you can write rules, you can use them to [filter targets](filter.md)
or to [check if a target satisfies them](satisfies.md).

[Return to the index to explore the other possibilities of the library](index.md)
