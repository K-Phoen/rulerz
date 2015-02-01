Usage
=====

This guide will show you how to filter any kind of collection using a simple
language.

Here is a summary of what you will have to do:

    * [instanciate the RulerZ engine](#step-1-instanciate the RulerZ engine) ;
    * [write a rule](#step-2-write-a-rule) ;
    * [filter your target](#step-3-filter-your-target).

## Context

In the following examples, we'll define rules to filter this collection:

```php
$users = [
    ['name' => 'Joe', 'group' => 'guest', 'points' => 40],
    ['name' => 'Moe', 'group' => 'guest', 'points' => 20],
];
```

## Step 1: Instanciate the RulerZ engine

In order to work, the `RulerZ` engine needs an interpreter and at least one
executor.
The interpreter will parse your rule while the executors will receive the parsed
rule and apply it to your target. An executor handles a specific type of target
(an array for instance), that's why you can have several executors registered
into the same RulerZ engine.

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

## Step 3: Filter your target

This is the easiest part. Define the parameters used in your rule (if any), and
enjoy!

```php
$parameters = [
    'min_points' => 30,
    'groups'     => ['customer', 'guest'],
];

var_dump($rulerz->filter($users, $rule, $parameters)); // the third parameter can be omitted if empty
/*
array(1) {
  [0]=>
  array(3) {
    ["name"]=>
    string(3) "Joe"
    ["group"]=>
    string(5) "guest"
    ["points"]=>
    int(40)
  }
}
*/
```

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
