<?php

namespace RulerZ\Compiler\Visitor\Elasticsearch;

use Hoa\Ruler\Model as AST;

use RulerZ\Compiler\Target\Polyfill;
use RulerZ\Compiler\Visitor\GenericVisitor;
use RulerZ\Model;

/**
 * Base class for Elasticsearch-related visitors.
 */
class GenericElasticsearchVisitor extends GenericVisitor
{
    use Polyfill\AccessPath;

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
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        $array = parent::visitArray($element, $handle, $eldnah);

        return sprintf('[%s]', implode(', ', $array));
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
            $value = is_array($b) ? '[' . implode(', ', $b) . ']' : $b;

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

        $this->setInlineOperator('!=', function ($a, $b) use ($mustNot) {
            return $mustNot("[
                'term' => [
                    '$a' => $b,
                ]
            ]");
        });

        $this->setInlineOperator('not', $mustNot);

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
    }
}
