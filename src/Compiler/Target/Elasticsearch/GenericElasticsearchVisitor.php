<?php

namespace RulerZ\Compiler\Target\Elasticsearch;

use Hoa\Ruler\Model as AST;

use RulerZ\Compiler\Target\GenericVisitor;
use RulerZ\Model;

/**
 * Base class for Elasticsearch-related visitors.
 */
abstract class GenericElasticsearchVisitor extends GenericVisitor
{
    /**
     * @inheritDoc
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
        return sprintf('$parameters["%s"]', $element->getName());
    }

    /**
     * {@inheritDoc}
     */
    public function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null)
    {
        $value = parent::visitScalar($element, $handle, $eldnah);

        return is_numeric($value) ? $value : sprintf("'%s'", $value);
    }

    /**
     * {@inheritDoc}
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        $array = parent::visitArray($element, $handle, $eldnah);

        return sprintf('(%s)', implode(', ', $array));
    }

    /**
     * @inheritDoc
     */
    protected function defineBuiltInOperators()
    {
        // start with a few helpers
        $must = function($query) {
            return "[
                'bool' => ['must' => $query]
            ]";
        };
        $mustNot = function($query) {
            return "[
                'bool' => ['must_not' => $query]
            ]";
        };
        $range = function($field, $value, $operator) use ($must) {
            return $must("[
                'range' => [
                    '$field' => ['$operator' => $value],
                ]
            ]");
        };

        // Here are the operators!
        $this->setInlineOperator('and', function ($a, $b) use ($must) {
            return $must("[$a, $b]");
        });
        $this->setInlineOperator('or', function ($a, $b) use ($must) {
            return "[
                'bool' => ['should' => [$a, $b], 'minimum_should_match' => 1]
            ]";
        });

        $this->setInlineOperator('like', function ($a, $b) use ($must) {
            $value = is_array($b) ? implode(' ', $b) : $b;

            return $must("[
                'match' => [
                    '$a' => '$value',
                ]
            ]");
        });
        $this->setInlineOperator('has', function ($a, $b) use ($must) {
            $value = is_array($b) ? $b : "'[$b]'";

            return $must("[
                'terms' => [
                    '$a' => $value,
                ]
            ]");
        });
        $this->setInlineOperator('in', $this->getInlineOperator('has'));

        $this->setInlineOperator('=', function ($a, $b) use ($must) {
            return $must("[
                'term' => [
                    '$a' => $b,
                ]
            ]");
        });

        $this->setInlineOperator('not', $mustNot);

        $this->setInlineOperator('match_all', function() {
            return "['match_all' => []]";
        });

        $this->setInlineOperator('>', function ($a, $b) use ($range) {
            return $range($a, $b, 'gt');
        });

        $this->setInlineOperator('>=', function ($a, $b) use ($range) {
            return $range($a, $b, 'gte');
        });

        $this->setInlineOperator('<', function ($a, $b) use ($range) {
            return $range($a, $b, 'lt');
        });

        $this->setInlineOperator('<=', function ($a, $b) use ($range) {
            return $range($a, $b, 'lte');
        });

        $this->setInlineOperator('!=', function ($a, $b) use ($mustNot) {
            $value = is_array($b) ? $b : "'[$b]'";

            return $mustNot("[
                'terms' => [
                    '$a' => $value,
                ]
            ]");
        });

        $this->setInlineOperator('in_envelope', function ($a, $b) use ($must) {
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
}
