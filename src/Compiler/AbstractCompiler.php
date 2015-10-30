<?php

namespace RulerZ\Compiler;

use RulerZ\Parser\Parser;

abstract class AbstractCompiler implements Compiler
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    protected function compileToSource($rule, Target\CompilationTarget $compilationTarget, array $parameters)
    {
        $ast           = $this->parser->parse($rule);
        $executorModel = $compilationTarget->compile($ast);

        $flattenedTraits = implode(PHP_EOL, array_map(function($trait) {
            return "\t" . 'use ' . $trait . ';';
        }, $executorModel->getTraits()));

        $extraCode = '';
        foreach ($executorModel->getCompiledData() as $key => $value) {
            $extraCode .= sprintf('private $%s = %s;' . PHP_EOL, $key, var_export($value, true));
        }

        return <<<EXECUTOR
namespace RulerZ\Compiled\Executor;

use RulerZ\Executor\Executor;

class {$parameters['className']} implements Executor
{
    $flattenedTraits

    $extraCode

    // $rule
    protected function execute(\$target, array \$operators, array \$parameters)
    {
        return {$executorModel->getCompiledRule()};
    }
}
EXECUTOR;
    }

    protected function getRuleIdentifier(Target\CompilationTarget $compilationTarget, $rule)
    {
        return hash('crc32b', get_class($compilationTarget) . $rule);
    }
}
