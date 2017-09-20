<?php

namespace RulerZ\Target\Native;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractCompilationTarget;

class Native extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new NativeVisitor($this->getOperators());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\ArrayTarget\FilterTrait',
            '\RulerZ\Executor\ArrayTarget\SatisfiesTrait',
            '\RulerZ\Executor\ArrayTarget\ArgumentUnwrappingTrait',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return NativeOperators::create(parent::getOperators());
    }
}
