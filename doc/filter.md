Using a rule to filter a target
===============================

This guide will show you how to filter any kind of target using a simple language.

Here is a summary of what you will have to do:

 * [instanciate the RulerZ engine](writing_rules.md#step-1-instanciate-the-rulerz-engine) ;
 * [write a rule](writing_rules.md#step-2-write-a-rule) ;
 * [filter your target](#filter-your-target).

## Context

In the following examples, we'll define rules to filter this collection:

```php
$users = [
    ['name' => 'Joe', 'group'    => 'guest', 'points' => 40],
    ['name' => 'Moe', 'customer' => 'guest', 'points' => 20],
    ['name' => 'Al',  'group'    => 'admin', 'points' => 60],
];
```

## Filter your target

Let's say that we want to retrieve the customers or the guests having at least
30 points.
The rule describing these constraints would look like this:

```php
$rule  = 'group in :groups and points > :min_points';
```

Where `:groups` and `:min_points` are parameters that we'll need to define as
an array:.

```php
$parameters = [
    'min_points' => 30,
    'groups'     => ['customer', 'guest'],
];
```

Once the rule is written and the parameters are defined, only the easiest part
remains: filtering the target.

```php
var_dump($rulerz->filter($users, $rule, $parameters)); // the parameters can be omitted if empty

// will return:
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

Yup, it's that easy.

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
