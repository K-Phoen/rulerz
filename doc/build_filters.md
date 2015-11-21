Using RulerZ to build filters
=============================

Sometimes RulerZ can get in your way by fetching the results of a query for you.
For instance, when a pagination or a custom hydration method is needed, RulerZ
will fetch all the objects matching a query without allowing any customization
on the results returned by the filter.

In these cases, it can be wise to use RulerZ to only **build the filter, without
executing it**. If we take DoctrineORM as an example, it would mean updating the
`QueryBuilder` object to match your rule but leaving the execution part up to
the user.

## Applying a rule on a target

To do this, `RulerZ` exposes a `applyFilter` method that can be used like this:

```php
$playersQueryBuilder = …;
$rule  = 'gender = "F"';

$updatedQueryBuilder = $rulerz->applyFilter($playersQueryBuilder, $rule);

$results = $updatedQueryBuilder->getQuery()->getResult('CustomHydrator');
```

Using this method, you'll get the female players, hydrated using the
`CustomHydrator` class.

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
