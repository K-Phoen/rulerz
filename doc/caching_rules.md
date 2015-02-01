Caching rules
=============

When a rule is interpreted, its object model is created. Luckily (or not), this
object representation is serializable. This allows us to cache or even persist
rules in the database.

This library comes with a `CachedInterpreter` which decorates another
interpreter to avoid re-interpreting rules each times they are used.

As cache layer relies on `doctrine/cache`, a number of cache backends are
available.

Using the `CachedInterpreter` is also pretty straightforward:

```php
use RulerZ\Interpreter\HoaInterpreter;
use RulerZ\Interpreter\CachedInterpreter;
use RulerZ\RulerZ;

$cache = new \Doctrine\Common\Cache\ArrayCache();
$interpreter = new CachedInterpreter(new HoaInterpreter(), $cache);

$rulerz = new RulerZ(
    $interpreter, [
        // a list of executors
    ]
);
```

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
