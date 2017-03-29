Building rules without using the DSL
====================================

If for some reason you do not want to use the built-in DSL, be it because you want to use your IDE's autocompletion or
because you prefer object-oriented APIs, know that it's possible!

The [`K-Phoen/rulerz-spec-builder`](https://github.com/K-Phoen/rulerz-spec-builder) package provides an object-oriented
API to build Specifications for RulerZ:

```php
// gender = "F" and points > 3000 becomes:
$spec = Expr::andX(
    Expr::equals('gender', 'F'),
    Expr::moreThan('points', 3000)
);

// (gender = "F" and points > 3000) or (gender = 'M' and points < 3000) becomes:
$spec = Expr::orX(
    Expr::andX(
        Expr::equals('gender', 'F'),
        Expr::moreThan('points', 3000)
    ),
    Expr::andX(
        Expr::equals('gender', 'M'),
        Expr::lessThan('points', 3000)
    )
);
```

See [the package's documentation](https://github.com/K-Phoen/rulerz-spec-builder) for more details.


## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
