Using Eloquent ORM
===================

[Eloquent ORM](http://www.pomm-project.org/) is one of the targets supported by RulerZ.

This cookbook will show you how to retrieve objects using Eloquent and RulerZ.

Here is a summary of what you will have to do:

 * [configure Eloquent](#configure-eloquent);
 * [configure RulerZ](#configure-rulerz);
 * [filter your target](#filter-your-target).

## Configure Eloquent

This subject won't be directly treated here. You can either follow the [official
documentation](http://laravel.com/docs/5.0/eloquent).

## Configure RulerZ

Once Eloquent is installed and configured we can the RulerZ engine:

```php
use RulerZ\Executor\EloquentExecutor;
use RulerZ\Interpreter\HoaInterpreter;
use RulerZ\RulerZ;

$rulerz = new RulerZ(
    new HoaInterpreter(), [
        new EloquentExecutor(), // this line is Eloquent-specific
        // other executors...
    ]
);
```

The only Eloquent-related configuration is the `EloquentExecutor` being added to the
list of the known executors.

## Filter your target

Now that both Eloquent and RulerZ are ready, you can use them to retrieve data.

The `EloquentExecutor` instance that we previously injected into the RulerZ engine
knows how to use both `Illuminate\Database\Query\Builder` and `Illuminate\Database\Eloquent\Builder`
instances, so the first step is to create a query builder:

```php
$queryBuilder = User::query(); // where "User" is an Eloquent model
```

And as usual, we call RulerZ with our target and our rule.
RulerZ will find the right executor for the given target and use it to filter
the data, or in our case to retrieve data from a database.

```php
$rule  = 'group in :groups and points > :points';
$parameters = [
    'points' => 30,
    'groups' => ['customer', 'guest'],
];

var_dump($rulerz->filter($queryBuilder, $rule, $parameters));
```

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)
