From rules to specifications
============================

Until now, we expressed our rules with a dedicated DSL, in a textual format.
While being very easy to write and understand, this representation as some
issues. For instance, we can not easily compose rules.

To solve this, RulerZ introduces a slightly modified version of the
[Specification pattern](http://en.wikipedia.org/wiki/Specification_pattern).

The idea is quite simple: instead of having a string representing a business
rule (which can be complex), we'll split this rule into independant parts, put
each one of them in a class and call them Specifications.

## Context

Let's say that we want to retrieve the customers or the guests having at least
30 points.
The rule describing these constraints would look like this:

```php
$rule = 'group in :groups and points > :min_points';
```

Where `:groups` and `:min_points` are parameters that we'll need to define as
an array:.

```php
$parameters = [
    'min_points' => 30,
    'groups'     => ['customer', 'guest'],
];
```

## Writing specifications

In the rule written above, It's easy to see that in fact, we're expressing two
constraints: we want to filter users by group AND by score/points.

So let's see how we would write the specifications for these constraints.

In the first one, we'll keep users belonging to the groups we want:

```php
use RulerZ\Spec\Specification;

class UserGroups implements Specification
{
    private $groups;

    public function __construct(array $groups)
    {
        $this->groups = $groups;
    }

    public function getRule()
    {
        return 'group IN :groups';
    }

    public function getParameters()
    {
        return [
            'groups' => $this->groups,
        ];
    }
}
```

You'll notice that a Specification is nothing more than a "wrapper" around a
rule (or at least its textual representation) and the parameters that are
needed.

**Important:** you'll also notice that the specification we just wrote
implements the `RulerZ\Spec\Specification` interface which defines the `getRule`
and the `getParameters` methods.

The constraint on the points can be expressed in the same way:

```php
use RulerZ\Spec\Specification;

class UserMinScore implements Specification
{
    private $min_score;

    public function __construct($min_score)
    {
        $this->min_score = $min_score;
    }

    public function getRule()
    {
        return 'points > :min_score';
    }

    public function getParameters()
    {
        return [
            'min_score' => $this->min_score,
        ];
    }
}
```

## Combining specifications

At this point, we're able to write very precise specifications (which can even
be unit tested if needed, how cool is that?) but we still need to be able to
combine them into more complex ones.

Fortunately, RulerZ comes with a set of general purpose specifications such as:

* `RulerZ\Spec\AndX([$spec, $otherSpec, ...])` which represents the **conjunction**
  of several specifications ;
* `RulerZ\Spec\OrX([$spec, $otherSpec, ...])` which represents the **disjunction**
  of several specifications ;
* `RulerZ\Spec\Not([$spec, $otherSpec, ...])` which represents the **negation**
  of a specification.

Our previous specifications can be combined using these classes:

```php
use RulerZ\Spec;

$interestingUsersSpec = Spec\AndX([
    new UserMinScore(30),
    new UserGroups(['customer', 'guest']),
]);
```

## Using specifications

RulerZ offers a few shortcuts to work with specification objects:

```php
// filtering a target using a specification
$rulerz->filterSpec($target, $interestingUsersSpec);

// checkinf if a target satisfies a specification
var_dump($rulerz->satisfiesSpec($target, $interestingUsersSpec));
```

## That was it!

[Return to the index to explore the other possibilities of the library](index.md)
