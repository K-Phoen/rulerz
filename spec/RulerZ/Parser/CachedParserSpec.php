<?php

namespace spec\RulerZ\Parser;

use Doctrine\Common\Cache\Cache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Model\Rule;
use RulerZ\Parser\Parser;

class CachedParserSpec extends ObjectBehavior
{
    function let(Parser $parser, Cache $cache)
    {
        $this->beConstructedWith($parser, $cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Parser\CachedParser');
    }

    function it_uses_the_wrapped_parser_when_cache_is_missed(Parser $parser, Cache $cache, Rule $ruleModel)
    {
        $rule = 'dummy rule';

        $cache->fetch($rule)->willReturn(false);
        $cache->save($rule, Argument::any(), 0)->shouldBeCalled();

        $parser->parse($rule)->willReturn($ruleModel);

        $this->parse($rule)->shouldHaveType('\RulerZ\Model\Rule');
    }

    function it_uses_the_cache_when_possible(Parser $parser, Cache $cache, Rule $ruleModel)
    {
        $rule = 'dummy rule';

        $cache->fetch($rule)->willReturn(serialize($ruleModel));

        $parser->parse()->shouldNotBeCalled();

        $this->parse($rule)->shouldHaveType(get_class($ruleModel));
    }
}
