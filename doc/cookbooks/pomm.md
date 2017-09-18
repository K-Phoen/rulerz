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
$rulerz = new RulerZ(
    $compiler, [
        new \RulerZ\Target\Pomm\Pomm(), // this line is Pomm-specific
        // other compilation targets...
    ]
);
```

The only Pomm-related configuration is the `Pomm` target being added to the list
of the known compilation targets.

## Filter your target

Now that both Pomm and RulerZ are ready, you can use them to retrieve data.

The `Pomm` instance that we previously injected into the RulerZ engine
only knows how to use `PommProject\ModelManager\Model\Model` so the first step
is to access the model to query:

```php
$playerModel = $pomm['my_db']->getModel('\MyDb\PublicSchema\PlayerModel');
```

And as usual, we call RulerZ with our target (the `Model` object) and our rule.
RulerZ will build the right executor for the given target and use it to filter
the data, or in our case to retrieve data from a database.

```php
$rule  = 'gender = :gender and points > :points';
$parameters = [
    'points' => 30,
    'gender' => 'M',
];

var_dump($rulerz->filter($playerModel, $rule, $parameters));
```

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)