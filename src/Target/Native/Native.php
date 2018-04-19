<?php

declare(strict_types=1);

namespace RulerZ\Target\Native;

use RulerZ\Compiler\Context;
use RulerZ\Executor\ArrayTarget\ArgumentUnwrappingTrait;
use RulerZ\Executor\ArrayTarget\FilterTrait;
use RulerZ\Executor\ArrayTarget\SatisfiesTrait;
use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\Operators\Definitions;

class Native extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
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
            FilterTrait::class,
            SatisfiesTrait::class,
            ArgumentUnwrappingTrait::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators(): Definitions
    {
        return NativeOperators::create(parent::getOperators());
    }
}
