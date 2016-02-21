<?php

namespace RulerZ\Compiler\Target;

use RulerZ\Compiler\Context;
use RulerZ\Model;

/**
 * Generic visitor intended to be extended.
 */
abstract class AbstractCompilationTarget implements CompilationTarget
{
    use Polyfill\Operators;

    /**
     * Create a rule visitor for a given compilation context.
     *
     * @param Context $context The compilation context.
     *
     * @return \RulerZ\Compiler\RuleVisitor
     */
    abstract protected function createVisitor(Context $context);

    abstract protected function getExecutorTraits();

    /**
     * @param array<callable> $operators A list of additional operators to register.
     * @param array<callable> $inlineOperators A list of additional inline operators to register.
     */
    public function __construct(array $operators = [], array $inlineOperators = [])
    {
        $this->setOperators($operators);
        $this->setOperators($inlineOperators);
    }

    /**
     * @inheritDoc
     */
    public function compile(Model\Rule $rule, Context $compilationContext)
    {
        $visitor = $this->createVisitor($compilationContext);
        $compiledCode = $visitor->visit($rule);

        return new Model\Executor(
            $this->getExecutorTraits(),
            $compiledCode,
            $visitor->getCompilationData()
        );
    }

    /**
     * @inheritDoc
     */
    public function createCompilationContext($target)
    {
        return new Context();
    }
}
