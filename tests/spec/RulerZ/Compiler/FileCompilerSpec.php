<?php

namespace spec\RulerZ\Compiler;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;

use RulerZ\Compiler\Context as CompilationContext;
use RulerZ\Compiler\Target\CompilationTarget;
use RulerZ\Model\Executor;
use RulerZ\Model\Rule;
use RulerZ\Parser\Parser;

class FileCompilerSpec extends ObjectBehavior
{
    /**
     * @var vfsStreamDirectory
     */
    private $codeDirectory;

    function let(Parser $parser)
    {
        $this->codeDirectory = vfsStream::setup('directory');

        $this->beConstructedWith($parser, $this->codeDirectory->url());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Compiler\FileCompiler');
        $this->shouldHaveType('RulerZ\Compiler\Compiler');
    }

    function it_can_compile_a_rule_to_an_executor(Parser $parser, Rule $ruleModel, CompilationTarget $compilationTarget, Executor $executorModel)
    {
        $rule = 'points > 42';
        $compilationContext = new CompilationContext();

        // the parser returns an AST
        $parser->parse($rule)->willReturn($ruleModel);

        // the compilation target uses the AST to build an executor model
        $compilationTarget->compile($ruleModel, $compilationContext)->willReturn($executorModel);

        // and the executor model has PHP code representing the rule
        $executorModel->getCompiledRule()->willReturn('true');
        $executorModel->getCompiledData()->willReturn([]);
        $executorModel->getTraits()->willReturn([
            '\RulerZ\Stub\Executor\FilterTraitStub',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ]);

        // the compiler returns an instance of the compiled Executor
        $executor = $this->compile($rule, $compilationTarget, $compilationContext);
        $executor->shouldHaveType('RulerZ\Executor\Executor');

        // and calling the compiler again does not fail
        $executor = $this->compile($rule, $compilationTarget, $compilationContext);
        $executor->shouldHaveType('RulerZ\Executor\Executor');
    }
}
