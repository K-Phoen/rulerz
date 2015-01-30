<?php

namespace spec\Interpreter;

use Doctrine\Common\Cache\Cache;
use Hoa\Ruler\Model\Model as AST;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Interpreter\Interpreter;

class CachedInterpreterSpec extends ObjectBehavior
{
    function it_is_initializable(Interpreter $interpreter, Cache $cache)
    {
        $this->beConstructedWith($interpreter, $cache);

        $this->shouldHaveType('Interpreter\CachedInterpreter');
    }

    function it_uses_the_wrapped_interpreter_when_cache_is_missed(Interpreter $interpreter, Cache $cache, AST $ast)
    {
        $rule = $this->getRule();

        $this->beConstructedWith($interpreter, $cache);

        $cache->contains($rule)->willReturn(false);
        $cache->save($rule, Argument::any())->shouldBeCalled();
        $cache->fetch()->shouldNotBeCalled();

        $interpreter->interpret($rule)->willReturn($ast);

        $this->interpret($rule)->shouldHaveType('Hoa\Ruler\Model\Model');
    }

    function it_uses_the_cache_when_possible(Interpreter $interpreter, Cache $cache, AST $ast)
    {
        $rule = $this->getRule();

        $this->beConstructedWith($interpreter, $cache);

        $cache->contains($rule)->willReturn(true);
        $cache->fetch($rule)->willReturn(serialize($ast));

        $interpreter->interpret()->shouldNotBeCalled();;

        $this->interpret($rule)->shouldHaveType(get_class($ast));
    }

    private function getRule()
    {
        return 'points > 30';
    }
}
