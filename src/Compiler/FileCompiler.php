<?php

namespace RulerZ\Compiler;

use RulerZ\Parser\Parser;

class FileCompiler extends AbstractCompiler
{
    /**
     * The directory in which the sources will be stored.
     *
     * @param Parser $parser    The parser used to build the AST.
     * @param string $directory The directory in which the compiled executors are stored. Defaults to the system's temp directory.
     */
    private $directory;

    public function __construct(Parser $parser, $directory = null)
    {
        parent::__construct($parser);

        $this->directory = $directory ?: sys_get_temp_dir();
    }

    /**
     * @inheritdoc
     */
    public function compile($rule, Target\CompilationTarget $target)
    {
        $ruleIdentifier = $this->getRuleIdentifier($target, $rule);
        $executorFqcn   = '\RulerZ\Compiled\Executor\\Executor_' . $ruleIdentifier;

        if (!class_exists($executorFqcn, false)) {
            $fileName = $this->directory . DIRECTORY_SEPARATOR . 'rulerz_executor_' . $ruleIdentifier;

            if (!file_exists($fileName)) {
                $source = $this->compileToSource($rule, $target, [
                    'className' => 'Executor_' . $ruleIdentifier
                ]);
                file_put_contents($fileName, '<?php'."\n".$source);
            }

            require $fileName;
        }

        return new $executorFqcn();
    }
}
