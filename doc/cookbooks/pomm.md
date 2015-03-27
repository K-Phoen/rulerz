Using Pomm
==========

[Pomm](http://www.pomm-project.org/) is one of the targets supported by RulerZ.

This cookbook will show you how to retrieve objects using Pomm and RulerZ.

Here is a summary of what you will have to do:

 * [configure Pomm](#configure-pomm);
 * [configure RulerZ](#configure-rulerz);
 * [filter your target](#filter-your-target).

## Configure Pomm

This subject won't be directly treated here. You can either follow the [official
documentation](http://www.pomm-project.org/documentation/sandbox2) or use a
bundle/module/whatever the framework you're using promotes.

## Configure RulerZ

Once Pomm is installed and configured we can the RulerZ engine:

```php
use RulerZ\Executor\PommExecutor;
use RulerZ\Interpreter\HoaInterpreter;
use RulerZ\RulerZ;

$rulerz = new RulerZ(
    new HoaInterpreter(), [
        new PommExecutor(), // this line is Pomm-specific
        // other executors...
    ]
);
```

The only Pomm-related configuration is the `PommExecutor` being added to the
list of the known executors.

## Filter your target

Now that both Pomm and RulerZ are ready, you can use them to retrieve data.

The `PommExecutor` instance that we previously injected into the RulerZ engine
only knows how to use `PommProject\ModelManager\Model\Model` so the first step
is to access the model to query:

```php
$usersModel = $pomm['my_db']->getModel('\MyDb\PublicSchema\UserModel');
```

And as usual, we call RulerZ with our target (the `Model` object) and our
rule.
RulerZ will find the right executor for the given target and use it to filter
the data, or in our case to retrieve data from a database.

```php
$rule  = 'group in :groups and points > :points';
$parameters = [
    'points' => 30,
    'groups' => ['customer', 'guest'],
];

var_dump($rulerz->filter($usersModel, $rule, $parameters));
```

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)
