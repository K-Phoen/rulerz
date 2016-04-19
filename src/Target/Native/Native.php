<?php

namespace RulerZ\Target\Native;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractCompilationTarget;

class Native extends AbstractCompilationTarget
{
    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        if ($mode === self::MODE_APPLY_FILTER) {
            return false;
        }

        // we can filter a collection
        if ($mode === self::MODE_FILTER) {
            return is_array($target) || $target instanceof \Traversable;
        }

        // and we know how to handle arrays and objects
        return is_array($target) || is_object($target);
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new NativeVisitor($this->getOperators());
    }

    /**
     * {@inheritDoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\ArrayTarget\FilterTrait',
            '\RulerZ\Executor\ArrayTarget\SatisfiesTrait',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return NativeOperators::create(parent::getOperators());
    }
}
