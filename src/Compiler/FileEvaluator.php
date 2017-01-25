<?php

namespace RulerZ\Compiler;

class FileEvaluator implements Evaluator
{
    private $directory;

    /**
     * @param string $directory The directory in which the compiled executors are stored. Defaults to the system's temp directory.
     */
    public function __construct($directory = null)
    {
        $this->directory = $directory ?: sys_get_temp_dir();
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($ruleIdentifier, callable $compiler)
    {
        $fileName = $this->directory.DIRECTORY_SEPARATOR.'rulerz_executor_'.$ruleIdentifier;

        if (!file_exists($fileName)) {
            file_put_contents($fileName, '<?php'."\n".$compiler());
        }

        require $fileName;
    }
}
