Using a rule to filter a target
===============================

This guide will show you how to filter any kind of target using a simple language.

Here is a summary of what you will have to do:

 * [instantiate the RulerZ engine](writing_rules.md#step-1-instantiate-the-rulerz-engine) ;
 * [write a rule](writing_rules.md#step-2-write-a-rule) ;
 * [filter your target](#filter-your-target).

## Context

In the following examples, we'll define rules to filter this collection:

```php
$users = [
    ['pseudo' => 'Joe',   'gender' => 'M', 'points' => 40],
    ['pseudo' => 'Moe',   'gender' => 'M', 'points' => 20],
    ['pseudo' => 'Alice', 'gender' => 'F', 'points' => 60],
];
```

## Filter your target

Let's say that we want to retrieve the female players having at least 30 points.
The rule describing these constraints would look like this:

```php
$rule  = 'gender = :gender and points > :min_points';
```

Where `:gender` and `:min_points` are parameters that we'll need to define as
an array:

```php
$parameters = [
    'min_points' => 30,
    'gender'     => 'F',
];
```

Once the rule is written and the parameters are defined, only the easiest part
remains: filtering the target.

```php
var_dump(
    iterator_to_array(
        $rulerz->filter($players, $rule, $parameters) // the parameters can be omitted if empty
    )
);

// will return:
/*
array(1) {
  [0]=>
  array(3) {
    ["pseudo"]=>
    string(5) "Alice"
    ["gender"]=>
    string(1) "F"
    ["points"]=>
    int(60)
  }
}
*/
```

Yup, it's that easy.

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
