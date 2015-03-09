<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;
use Hoa\Visitor\Visit as Visitor;

class ElasticsearchVisitor implements Visitor
{
    use Polyfill\CustomOperators;

    /**
     * List of parameters.
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Constructor.
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;

        $this->defineBuiltInOperators();
    }

    /**
     * Visit an element.
     *
     * @param \VisitorElement $element Element to visit.
     * @param mixed           &$handle Handle (reference).
     * @param mixed           $eldnah  Handle (not reference).
     *
     * @return string The DQL code for the given rule.
     */
    public function visit(VisitorElement $element, &$handle = null, $eldnah = null)
    {
        if ($element instanceof AST\Model) {
            return $this->visitModel($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Operator) {
            return $this->visitOperator($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Bag\Scalar) {
            return $this->visitScalar($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Bag\RulerArray) {
            return $this->visitArray($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Bag\Context) {
            return $this->visitContext($element, $handle, $eldnah);
        }

        throw new \LogicException(sprintf('Element of type "%s" not handled', get_class($element)));
    }

    /**
     * Visit a model
     *
     * @param AST\Model $element Element to visit.
     * @param mixed     &$handle Handle (reference).
     * @param mixed     $eldnah  Handle (not reference).
     *
     * @return string
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }

    /**
     * Visit a context (ie: a column access or a parameter)
     *
     * @param AST\Bag\Context $element Element to visit.
     * @param mixed           &$handle Handle (reference).
     * @param mixed           $eldnah  Handle (not reference).
     *
     * @return string
     */
    private function visitContext(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $name = $element->getId();

        // nested path
        $dimensions = $element->getDimensions();
        if (!empty($dimensions)) {
            return $this->flattenAccessPath($element);
        }

        // parameter
        if ($name[0] === ':') {
            return $this->lookupParameter(substr($name, 1));
        }

        return $element->getId();
    }

    /**
     * @param AST\Bag\Context $element Element to visit.
     *
     * @return string
     */
    private function flattenAccessPath(AST\Bag\Context $element)
    {
        $flattenedDimensions = [$element->getId()];
        foreach ($element->getDimensions() as $dimension) {
            $flattenedDimensions[] = $dimension[1];
        }

        return implode('.', $flattenedDimensions);
    }

    /**
     * Visit a scalar
     *
     * @param AST\Bag\Scalar $element Element to visit.
     * @param mixed          &$handle Handle (reference).
     * @param mixed          $eldnah  Handle (not reference).
     *
     * @return string
     */
    private function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null)
    {
        return $element->getValue();
    }

    /**
     * Visit an array
     *
     * @param AST\Bag\RulerArray $element Element to visit.
     * @param mixed              &$handle Handle (reference).
     * @param mixed              $eldnah  Handle (not reference).
     *
     * @return string
     */
    private function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        return array_map(function ($item) use ($handle, $eldnah) {
            return $item->accept($this, $handle, $eldnah);
        }, $element->getArray());
    }

    /**
     * Visit an operator
     *
     * @param AST\Operator $element Element to visit.
     * @param mixed        &$handle Handle (reference).
     * @param mixed        $eldnah  Handle (not reference).
     *
     * @return string
     */
    private function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        $xcallable = $this->getOperator($element->getName());

        $arguments = array_map(function ($argument) use ($handle, $eldnah) {
            return $argument->accept($this, $handle, $eldnah);
        }, $element->getArguments());

        return $xcallable->distributeArguments($arguments);
    }

    /**
     * Return the value of a parameter.
     *
     * @param string $name The parameter's name.
     *
     * @return mixed
     */
    private function lookupParameter($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \RuntimeException(sprintf('Parameter "%s" not defined', $name));
        }

        return $this->parameters[$name];
    }

    /**
     * Define the built-in operators.
     */
    private function defineBuiltInOperators()
    {
        // start with a few helpers
        $must = function($query) {
            return [
                'bool' => ['must' => $query]
            ];
        };
        $mustNot = function($query) {
            return [
                'bool' => ['must_not' => $query]
            ];
        };
        $range = function($field, $value, $operator) use ($must) {
            return $must([
                'range' => [
                    $field => [$operator => $value],
                ]
            ]);
        };

        // Here are the operators!
        $this->setOperator('and', function ($a, $b) use ($must) {
            return $must([$a, $b]);
        });
        $this->setOperator('or', function ($a, $b) use ($must) {
            return [
                'bool' => ['should' => [$a, $b], 'minimum_should_match' => 1]
            ];
        });

        $this->setOperator('like', function ($a, $b) use ($must) {
            return $must([
                'terms' => [
                    $a => is_array($b) ? $b : [$b],
                ]
            ]);
        });
        $this->setOperator('has', $this->getOperator('like'));
        $this->setOperator('in', $this->getOperator('like'));

        $this->setOperator('=', function ($a, $b) use ($must) {
            return $must([
                'term' => [
                    $a => $b,
                ]
            ]);
        });

        $this->setOperator('not', $mustNot);

        $this->setOperator('match_all', function() {
            return ['match_all' => []];
        });

        $this->setOperator('>', function ($a, $b) use ($range) {
            return $range($a, $b, 'gt');
        });

        $this->setOperator('>=', function ($a, $b) use ($range) {
            return $range($a, $b, 'gte');
        });

        $this->setOperator('<', function ($a, $b) use ($range) {
            return $range($a, $b, 'lt');
        });

        $this->setOperator('<=', function ($a, $b) use ($range) {
            return $range($a, $b, 'lte');
        });

        $this->setOperator('!=', function ($a, $b) use ($mustNot) {
            return $mustNot([
                'terms' => [
                    $a => is_array($b) ? $b : [$b],
                ]
            ]);
        });

        $this->setOperator('in_envelope', function ($a, $b) use ($must) {
            return $must([
                'geo_shape' => [
                    $a => [
                        'shape' => [
                            'type'        => 'envelope',
                            'coordinates' => $b,
                        ]
                    ]
                ]
            ]);
        });
    }
}
