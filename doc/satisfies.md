Checking if a target satisfies a rule
=====================================

This guide will show you how to filter any kind of collection using a simple
language.

Here is a summary of what you will have to do:

 * [instantiate the RulerZ engine](writing_rules.md#step-1-instanciate-the-rulerz-engine) ;
 * [write a rule](writing_rules.md#step-2-write-a-rule) ;
 * [check if the target satisfies the rule](#check-if-the-target-satisfies-the-rule).

## Context

In the following examples, we'll try to check if the following "player" satisfies
a rule:

```php
$player = [
    'pseudo' => 'Joe',
    'gender' => 'M',
    'points' => 40,
];
```

## Check if the target satisfies the rule

Let's say that we want to check if the given player is a male and has at least
30 points (don't ask why).
The rule describing these constraints would look like this:

```php
$rule  = 'gender = :gender and points > :min_points';
```

Where `:gender` and `:min_points` are parameters that we'll need to define as
an array:

```php
$parameters = [
    'min_points' => 30,
    'gender'     => 'M',
];
```

Once the rule is written and the parameters are defined, only the easiest part
remains:

```php
var_dump($rulerz->satisfies($player, $rule, $parameters)); // the parameters can be omitted if empty

// will return:
/*
bool(true)
*/
```

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
