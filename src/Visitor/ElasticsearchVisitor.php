<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;

use RulerZ\Model;

class ElasticsearchVisitor extends GenericVisitor
{
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
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $dimensions = $element->getDimensions();

        // nested path
        if (!empty($dimensions)) {
            return $this->flattenAccessPath($element);
        }

        return $element->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        return $this->lookupParameter($element->getName());
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
     * Return the value of a parameter.
     *
     * @param string $name The parameter's name.
     *
     * @return mixed
     */
    private function lookupParameter($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \RuntimeException(sprintf('Parameter "%s" not defined', $name)); // @todo this should be a more specific exception
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
                'match' => [
                    $a => is_array($b) ? implode(' ', $b) : $b,
                ]
            ]);
        });
        $this->setOperator('has', function ($a, $b) use ($must) {
            return $must([
                'terms' => [
                    $a => is_array($b) ? $b : [$b],
                ]
            ]);
        });
        $this->setOperator('in', $this->getOperator('has'));

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
