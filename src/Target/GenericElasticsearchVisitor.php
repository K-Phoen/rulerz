<?php

namespace RulerZ\Target;

use Hoa\Ruler\Model as AST;

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
}
