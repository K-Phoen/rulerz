Using Doctrine ORM
==================

[Doctrine ORM](http://www.doctrine-project.org/projects/orm.html) is one of the
targets supported by RulerZ.

This cookbook will show you how to retrieve objects using Doctrine and RulerZ.

Here is a summary of what you will have to do:

 * [configure Doctrine ORM](#configure-doctrine-orm);
 * [configure RulerZ](#configure-rulerz);
 * [filter your target](#filter-your-target).

## Configure Doctrine ORM

This subject won't be directly treated here. You can either follow the [official
documentation](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/configuration.html)
or use a bundle/module/whatever the framework you're using promotes.

## Configure RulerZ

Once Doctrine is installed and configured we can the RulerZ engine:

```php
use RulerZ\Executor\DoctrineQueryBuilderExecutor;
use RulerZ\Interpreter\HoaInterpreter;
use RulerZ\RulerZ;

$rulerz = new RulerZ(
    new HoaInterpreter(), [
        new DoctrineQueryBuilderExecutor(), // this line is Doctrine-specific
        // other executors...
    ]
);
```

The only Doctrine-related configuration is the `DoctrineQueryBuilderExecutor`
being added to the list of the known executors.

## Filter your target

Now that both Doctrine and RulerZ are ready, you can use them to retrieve data.

The `DoctrineQueryBuilderExecutor` instance that we previously injected into the
RulerZ engine only knows how to use `QueryBuilder`s so the first step is to
create one:

```php
$usersQueryBuilder = $entityManager
    ->createQueryBuilder()
    ->select('u')
    ->from('Entity\User', 'u');
```

And as usual, we call RulerZ with our target (the `QueryBuilder` object) and our
rule.
RulerZ will find the right executor for the given target and use it to filter
the data, or in our case to retrieve data from a database.

```php
$rule  = 'group in :groups and points > :points';
$parameters = [
    'points' => 30,
    'groups' => ['customer', 'guest'],
];

var_dump($rulerz->filter($usersQueryBuilder, $rule, $parameters));
```

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)
