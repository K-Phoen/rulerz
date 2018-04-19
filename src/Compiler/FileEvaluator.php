<?php

declare(strict_types=1);

namespace RulerZ\Compiler;

class FileEvaluator implements Evaluator
{
    /** @var string */
    private $directory;

    /** @var Filesystem */
    private $fs;

    /**
     * @param string $directory The directory in which the compiled executors are stored. Defaults to the system's temp directory.
     */
    public function __construct($directory = null, Filesystem $fs = null)
    {
        $this->directory = $directory ?: sys_get_temp_dir();
        $this->fs = $fs ?: new NativeFilesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(string $ruleIdentifier, callable $compiler): void
    {
        $fileName = $this->directory.DIRECTORY_SEPARATOR.'rulerz_executor_'.$ruleIdentifier;

        if (!$this->fs->has($fileName)) {
            $this->fs->write($fileName, '<?php'."\n".$compiler());
        }

        require $fileName;
    }
}
