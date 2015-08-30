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
$rulerz = new RulerZ(
    $compiler, [
        new \RulerZ\Compiler\Target\Sql\DoctrineQueryBuilderVisitor(), // this line is Doctrine-specific
        // other compilation targets...
    ]
);
```

The only Doctrine-related configuration is the `DoctrineQueryBuilderVisitor`
being added to the list of the known compilation targets.

## Filter your target

Now that both Doctrine and RulerZ are ready, you can use them to retrieve data.

The `DoctrineQueryBuilderVisitor` instance that we previously injected into the
RulerZ engine only knows how to use `QueryBuilder`s so the first step is to
create one:

```php
$playersQueryBuilder = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Player', 'p');
```

And as usual, we call RulerZ with our target (the `QueryBuilder` object) and our
rule.
RulerZ will build the right executor for the given target and use it to filter
the data, or in our case to retrieve data from a database.

```php
$rule  = 'gender = :gender and points > :points';
$parameters = [
    'points' => 30,
    'gender' => 'M',
];

var_dump($rulerz->filter($playersQueryBuilder, $rule, $parameters));
```

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)