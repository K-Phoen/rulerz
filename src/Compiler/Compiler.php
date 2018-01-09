<?php

namespace RulerZ\Compiler;

use RulerZ\Executor\Executor;
use RulerZ\Parser\Parser;
use RulerZ\Target\Operators\CompileTimeOperator;

class Compiler
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Evaluator
     */
    private $evaluator;

    public static function create($cacheDirectory = null)
    {
        return new static(new FileEvaluator($cacheDirectory));
    }

    public function __construct(Evaluator $evaluator)
    {
        $this->parser = new Parser();
        $this->evaluator = $evaluator;
    }

    /**
     * @return Executor
     */
    public function compile($rule, CompilationTarget $target, Context $context)
    {
        $context['rule_identifier'] = $this->getRuleIdentifier($target, $context, $rule);
        $context['executor_classname'] = 'Executor_'.$context['rule_identifier'];
        $context['executor_fqcn'] = '\RulerZ\Compiled\Executor\\Executor_'.$context['rule_identifier'];

        if (!class_exists($context['executor_fqcn'], false)) {
            $compiler = function () use ($rule, $target, $context) {
                return $this->compileToSource($rule, $target, $context);
            };

            $this->evaluator->evaluate($context['rule_identifier'], $compiler);
        }

        return new $context['executor_fqcn']();
    }

    protected function compileToSource($rule, CompilationTarget $compilationTarget, Context $context)
    {
        $ast = $this->parser->parse($rule);
        $executorModel = $compilationTarget->compile($ast, $context);

        $flattenedTraits = implode(PHP_EOL, array_map(function ($trait) {
            return "\t".'use \\'.ltrim($trait, '\\').';';
        }, $executorModel->getTraits()));

        $extraCode = '';
        foreach ($executorModel->getCompiledData() as $key => $value) {
            $extraCode .= sprintf('private $%s = %s;'.PHP_EOL, $key, var_export($value, true));
        }

        $commentedRule = str_replace(PHP_EOL, PHP_EOL.'    // ', $rule);
        $compiledRule = $executorModel->getCompiledRule();
        $escapedRule = is_string($compiledRule) ? $compiledRule : $compiledRule->format(false);

        return <<<EXECUTOR
namespace RulerZ\Compiled\Executor;

use RulerZ\Executor\Executor;

class {$context['executor_classname']} implements Executor
{
    $flattenedTraits

    $extraCode

    // $commentedRule
    protected function execute(\$target, array \$operators, array \$parameters)
    {
        return {$escapedRule};
    }
}
EXECUTOR;
    }

    protected function getRuleIdentifier(CompilationTarget $compilationTarget, Context $context, $rule)
    {
        return hash('crc32b', get_class($compilationTarget).$rule.$compilationTarget->getRuleIdentifierHint($rule, $context));
    }
}
