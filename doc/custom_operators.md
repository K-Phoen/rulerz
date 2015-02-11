Defning custom operators
========================

Most of the executors support custom operators (hint: if they implement
`ExtendableExecutor`, they do).

## Custom operators for the ArrayExecutor

The `ArrayExecutor` filters existing collections so the operators are simple
callables evaluated on the fly.

You can register a new operator directly in the constructor or by calling the
`registerOperators` method. Both ways are strictly equivalent.

```php
$executor = new ArrayExecutor([
    'length' => 'strlen',
]);

$executor->registerOperators([
    'logged' => function($user) {
        return return $user::CONNECTED === $user->getStatus();
    },
]);
```

## Custom operators for the DoctrineQueryBuilderExecutor

This executor is a bit different than the last one as it does not filter an
existing collection but transforms a rule in a query.
Because of this, the callables must return the DQL equivalent of the operator
being defined.

```php
$executor = new DoctrineQueryBuilderExecutor([
    'like' => function ($a, $b) {
        return sprintf('%s LIKE %s', $a, $b);
    },
]);
```

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
