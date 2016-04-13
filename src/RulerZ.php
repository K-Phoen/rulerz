<?php

namespace RulerZ;

use RulerZ\Compiler\Compiler;
use RulerZ\Compiler\Target\CompilationTarget;
use RulerZ\Context\ExecutionContext;
use RulerZ\Exception\TargetUnsupportedException;
use RulerZ\Spec\Specification;

class RulerZ
{
    /**
     * @var array<CompilationTarget> $compilationTargets
     */
    private $compilationTargets = [];

    /**
     * Constructor.
     *
     * @param Compiler $compiler           The compiler.
     * @param array    $compilationTargets A list of target compilers, each one handles a specific target (an array, a DoctrineQueryBuilder, ...)
     */
    public function __construct(Compiler $compiler, array $compilationTargets = [])
    {
        $this->compiler = $compiler;

        foreach ($compilationTargets as $targetCompiler) {
            $this->registerCompilationTarget($targetCompiler);
        }
    }

    /**
     * Registers a new target compiler.
     *
     * @param CompilationTarget $compilationTarget The target compiler to register.
     */
    public function registerCompilationTarget(CompilationTarget $compilationTarget)
    {
        $this->compilationTargets[] = $compilationTarget;
    }

    /**
     * Apply the filters on the target using the given rule and parameters.
     * The target compiler to use is determined at runtime using the registered ones.
     *
     * @param mixed  $target           The target to filter.
     * @param string $rule             The rule to apply.
     * @param array  $parameters       The parameters used in the rule.
     * @param array  $executionContext The execution context.
     *
     * @return mixed
     */
    public function applyFilter($target, $rule, array $parameters = [], array $executionContext = [])
    {
        $parameters = $this->normalizeParameters($parameters);
        $targetCompiler = $this->findTargetCompiler($target, CompilationTarget::MODE_APPLY_FILTER);
        $executor       = $this->compiler->compile($rule, $targetCompiler);

        return $executor->applyFilter($target, $parameters, $targetCompiler->getOperators(), new ExecutionContext($executionContext));
    }

    /**
     * Filters a target using the given rule and parameters.
     * The target compiler to use is determined at runtime using the registered ones.
     *
     * @param mixed  $target           The target to filter.
     * @param string $rule             The rule to apply.
     * @param array  $parameters       The parameters used in the rule.
     * @param array  $executionContext The execution context.
     *
     * @return \Traversable The filtered target.
     */
    public function filter($target, $rule, array $parameters = [], array $executionContext = [])
    {
        $parameters = $this->normalizeParameters($parameters);
        $targetCompiler = $this->findTargetCompiler($target, CompilationTarget::MODE_FILTER);
        $executor       = $this->compiler->compile($rule, $targetCompiler);

        return $executor->filter($target, $parameters, $targetCompiler->getOperators(), new ExecutionContext($executionContext));
    }

    /**
     * Filters a target using the given specification.
     * The targetCompiler to use is determined at runtime using the registered ones.
     *
     * @param mixed         $target           The target to filter.
     * @param Specification $spec             The specification to apply.
     * @param array         $executionContext The execution context.
     *
     * @return mixed The filtered target.
     */
    public function filterSpec($target, Specification $spec, array $executionContext = [])
    {
        return $this->filter($target, $spec->getRule(), $spec->getParameters(), $executionContext);
    }

    /**
     * Apply the filters on a target using the given specification.
     * The targetCompiler to use is determined at runtime using the registered ones.
     *
     * @param mixed         $target           The target to filter.
     * @param Specification $spec             The specification to apply.
     * @param array         $executionContext The execution context.
     */
    public function applyFilterSpec($target, Specification $spec, array $executionContext = [])
    {
        return $this->applyFilter($target, $spec->getRule(), $spec->getParameters(), $executionContext);
    }

    /**
     * Tells if a target satisfies the given rule and parameters.
     * The target compiler to use is determined at runtime using the registered ones.
     *
     * @param mixed  $target           The target.
     * @param string $rule             The rule to test.
     * @param array  $parameters       The parameters used in the rule.
     * @param array  $executionContext The execution context.
     *
     * @return boolean
     */
    public function satisfies($target, $rule, array $parameters = [], array $executionContext = [])
    {
        $targetCompiler = $this->findTargetCompiler($target, CompilationTarget::MODE_SATISFIES);
        $executor       = $this->compiler->compile($rule, $targetCompiler);

        return $executor->satisfies($target, $parameters, $targetCompiler->getOperators(), new ExecutionContext($executionContext));
    }

    /**
     * Tells if a target satisfies the given specification.
     * The target compiler to use is determined at runtime using the registered ones.
     *
     * @param mixed         $target           The target.
     * @param Specification $spec             The specification to use.
     * @param array         $executionContext The execution context.
     *
     * @return boolean
     */
    public function satisfiesSpec($target, Specification $spec, array $executionContext = [])
    {
        return $this->satisfies($target, $spec->getRule(), $spec->getParameters(), $executionContext);
    }

    /**
     * Finds a target compiler supporting the given target.
     *
     * @param mixed  $target The target to filter.
     * @param string $mode   The execution mode (MODE_FILTER or MODE_SATISFIES).
     *
     * @throws TargetUnsupportedException
     *
     * @return CompilationTarget
     */
    private function findTargetCompiler($target, $mode)
    {
        /** @var CompilationTarget $targetCompiler */
        foreach ($this->compilationTargets as $targetCompiler) {
            if ($targetCompiler->supports($target, $mode)) {
                return $targetCompiler;
            }
        }

        throw new TargetUnsupportedException('The given target is not supported.');
    }
    
    /**
     * Ensures positional parameters are prefixed with a string
     * 
     * @param array $params The parameters to normalize
     * 
     * @return array
     */
    private function normalizeParameters(array $params) 
    {
        $normalizedParams = [];
        $prefix = '_';
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $key = $prefix.(string) $key;
            }
            $normalizedParams[$key] = $value;
        }
        return $normalizedParams;
    }
}
