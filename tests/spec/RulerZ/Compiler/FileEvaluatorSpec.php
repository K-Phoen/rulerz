<?php

namespace spec\RulerZ\Compiler;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;

class FileEvaluatorSpec extends ObjectBehavior
{
    /**
     * @var vfsStreamDirectory
     */
    private $codeDirectory;

    public function let()
    {
        $this->codeDirectory = vfsStream::setup('some_directory', null, [
            'rulerz_executor_foo' => '<?php class DummyExecutor {}',
        ]);

        $this->beConstructedWith($this->codeDirectory->url());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Compiler\FileEvaluator');
        $this->shouldHaveType('RulerZ\Compiler\Evaluator');
    }

    public function it_can_evaluate_a_rule_from_an_existing_file()
    {
        $ruleIdentifier = 'foo';
        $compilerCallable = function () {
        };

        $this->evaluate($ruleIdentifier, $compilerCallable);
        $this->shouldHaveLoaded('DummyExecutor');
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

    public function getMatchers()
    {
        return [
            'haveLoaded' => function ($subject, $class) {
                return class_exists($class, false);
            },
        ];
    }
}
