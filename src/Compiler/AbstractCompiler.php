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

        return <<<EXECUTOR
namespace RulerZ\Compiled\Executor;

class {$parameters['className']}
{
    $flattenedTraits

    protected function execute(\$target, array \$operators, array \$parameters)
    {
        {$executorModel->getInitializationCode()}

        return {$executorModel->getCompiledRule()};
    }
}
EXECUTOR;
    }

    protected function getRuleIdentifier($rule)
    {
        return crc32($rule);
    }
}
