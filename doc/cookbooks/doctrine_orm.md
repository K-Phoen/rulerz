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
        new \RulerZ\Compiler\Target\Sql\DoctrineQueryBuilder(), // this line is Doctrine-specific
        // other compilation targets...
    ]
);
```

The only Doctrine-related configuration is the `DoctrineQueryBuilder` target
being added to the list of the known compilation targets.

## Filter your target

Now that both Doctrine and RulerZ are ready, you can use them to retrieve data.

The `DoctrineQueryBuilder` instance that we previously injected into the RulerZ
engine only knows how to use `QueryBuilder`s so the first step is to create one:

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

## Handling joins

More often that not, your entities will have relationships with other entities
in your application.

Let's imagine that our `Entity\Player` entity has a 1-1 association with a
`Entity\Group` entity and that we want to retrieve all the players that are in
a group having the role *ROLE_ADMIN*.

There are two ways to write rules using that association. In the first one, we
let RulerZ automatically determine how to join the entities:

```php
$playersQueryBuilder = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Player', 'p');

$rule = '"ROLE_ADMIN" IN group.roles';

var_dump($rulerz->filter($playersQueryBuilder, $rule));
```

It's important to notice that `group` is not an ordinary attribute: it's another
entity, joined by RulerZ.

**N.B:** RulerZ will call the `join()` method on the query builder, so it will
perform INNER joins by default.

If you need more control on how the joins are handled, we can prepare the query
builder and join the entities you need ourselves:

```php
$playersQueryBuilder = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Player', 'p')
    ->innerJoin('Entity\Group', 'g');

$rule = '"ROLE_ADMIN" IN g.roles';

var_dump($rulerz->filter($playersQueryBuilder, $rule));
```

This time, RulerZ is smart enough to understant that `g` might be a joined
entity and that it should not try to join it itself.

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)
