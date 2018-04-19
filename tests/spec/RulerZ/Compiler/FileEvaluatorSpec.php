<?php

namespace spec\RulerZ\Compiler;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;
use RulerZ\Compiler\Evaluator;
use RulerZ\Compiler\FileEvaluator;
use RulerZ\Compiler\Filesystem;

class FileEvaluatorSpec extends ObjectBehavior
{
    /**
     * @var vfsStreamDirectory
     */
    private $codeDirectory;

    public function let()
    {
        $this->codeDirectory = vfsStream::setup('some_directory', null, [
            'rulerz_executor_foo' => '<?php class SomeDummyExecutor {}',
        ]);

        $this->beConstructedWith($this->codeDirectory->url(), new VfsFilesystem());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FileEvaluator::class);
        $this->shouldHaveType(Evaluator::class);
    }

    public function it_can_evaluate_a_rule_from_an_existing_file()
    {
        $ruleIdentifier = 'foo';
        $compilerCallable = function () {
        };

        $this->evaluate($ruleIdentifier, $compilerCallable);
        $this->shouldHaveLoaded('SomeDummyExecutor');
    }

    public function it_uses_the_compiler_if_no_file_exists()
    {
        $ruleIdentifier = 'identifier that does not already exists';
        $compilerCallable = function () {
            return 'class NewDummyExecutor {}';
        };

        $this->evaluate($ruleIdentifier, $compilerCallable);
        $this->shouldHaveLoaded('NewDummyExecutor');
    }

    public function getMatchers(): array
    {
        return [
            'haveLoaded' => function ($subject, $class): bool {
                return class_exists($class, false);
            },
        ];
    }
}

class VfsFilesystem implements Filesystem
{
    public function has(string $filePath): bool
    {
        return file_exists($filePath);
    }

    public function write(string $filePath, string $content): void
    {
        file_put_contents($filePath, $content);
    }
}
