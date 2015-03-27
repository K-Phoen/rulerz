Using Elasticsearch and ruflin/Elastica
=======================================

[ruflin/Elastica](https://github.com/ruflin/Elastica) is one of the
targets supported by RulerZ. It allows tne engine to query an Elasticsearch
server.

This cookbook will show you how to retrieve objects using ruflin/Elastica and
RulerZ.

Here is a summary of what you will have to do:

 * [configure ruflin/Elastica](#configure-ruflin-elastica);
 * [configure RulerZ](#configure-rulerz);
 * [filter your target](#filter-your-target).

## Configure ruflin/Elastica

This subject won't be directly treated here. You can either follow the [official
documentation](http://elastica.io/getting-started/installation.html)
or use a bundle/module/whatever the framework you're using promotes.

## Configure RulerZ

Once ruflin/elastica is installed and configured we can the RulerZ engine:

```php
use RulerZ\Executor\ElasticaExecutor;
use RulerZ\Interpreter\HoaInterpreter;
use RulerZ\RulerZ;

$rulerz = new RulerZ(
    new HoaInterpreter(), [
        new ElasticaExecutor(), // this line is Elastica-specific
        // other executors...
    ]
);
```

The only Elastica-related configuration is the `ElasticaExecutor` being added
to the list of the known executors.

## Filter your target

Now that both ruflin/Elastica and RulerZ are ready, you can use them to retrieve
data.

The `ElasticaExecutor` instance that we previously injected into the RulerZ
engine knows how to use the following objects:

* `Elastica\Search`;
* `Elastica\SearchableInterface`.

So as long as you provide RulerZ with an object satisfying the previous
type-constraints, it will be able to use Elastica.

This example will show you how to use RulerZ in conjunction with a `Search`
object:

```php
$search = new Elastica\Search($client);
```

And as usual, we call RulerZ with our target (the `Search` object) and our
rule.
RulerZ will find the right executor for the given target and use it to filter
the data, or in our case to retrieve data from Elasticsearch.

```php
$rule  = 'group in :groups and points > :points';
$parameters = [
    'points' => 30,
    'groups' => ['customer', 'guest'],
];

var_dump($rulerz->filter($search, $rule, $parameters));
```

## That was it!

[Return to the index to explore the other possibilities of the library](../index.md)
