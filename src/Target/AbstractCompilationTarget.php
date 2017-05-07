<?php

namespace RulerZ\Target;

use RulerZ\Compiler\Context;
use RulerZ\Compiler\CompilationTarget;
use RulerZ\Model;
use RulerZ\Target\Operators\Definitions as OperatorsDefinitions;

/**
 * Generic visitor intended to be extended.
 */
abstract class AbstractCompilationTarget implements CompilationTarget
{
    /**
     * @var OperatorsDefinitions
     */
    private $customOperators;

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
        $this->customOperators = new OperatorsDefinitions($operators, $inlineOperators);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function createCompilationContext($target)
    {
        return new Context();
    }

    /**
     * {@inheritdoc}
     */
    public function defineOperator($name, callable $transformer)
    {
        $this->customOperators->defineOperator($name, $transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function defineInlineOperator($name, callable $transformer)
    {
        $this->customOperators->defineInlineOperator($name, $transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return $this->customOperators;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleIdentifierHint($rule, Context $context)
    {
        return '';
    }
}
