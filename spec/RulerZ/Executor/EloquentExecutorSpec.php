<?php

namespace spec\RulerZ\Executor;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Executor\Executor;

class EloquentExecutorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Executor\EloquentExecutor');
    }

    function it_supports_satisfies_mode(QueryBuilder $qb)
    {
        $this->supports($qb, Executor::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_query_builders(QueryBuilder $qb)
    {
        $this->supports($qb, Executor::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_filter_eloquent_builders(EloquentBuilder $qb)
    {
        $this->supports($qb, Executor::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_not_filter_other_types()
    {
        foreach ($this->unsupportedTypes() as $type) {
            $this->supports($type, Executor::MODE_FILTER)->shouldReturn(false);
        }
    }

    function it_can_filter_a_query_builder_with_a_rule(QueryBuilder $qb, ExecutionContext $context)
    {
        $qb->whereRaw('points > 30', [])->shouldBeCalled();
        $qb->get()->willReturn('result');

        $this->filter($qb, $this->getSimpleRule(), [], $context)->shouldReturn('result');
    }

    function it_supports_custom_operators(QueryBuilder $qb, ExecutionContext $context)
    {
        $this->registerOperators([
            'always_true' => function() {
                return '1 = 1';
            }
        ]);

        $qb->whereRaw('(points > 30 AND 1 = 1)', [])->shouldBeCalled();
        $qb->get()->willReturn('result');

        $this->filter($qb, $this->getCustomOperatorRule(), [], $context)->shouldReturn('result');
    }

    function it_implicitly_converts_unknown_operators(QueryBuilder $qb, ExecutionContext $context)
    {
        $qb->whereRaw('(points > 30 AND always_true())', [])->shouldBeCalled();
        $qb->get()->willReturn('result');

        $this->filter($qb, $this->getCustomOperatorRule(), [], $context)->shouldReturn('result');
    }

    private function unsupportedTypes()
    {
        return [
            'string',
            42,
            new \stdClass,
            [],
        ];
    }

    private function getSimpleRule()
    {
        // serialized rule for "points > 30"
        $rule = 'O:21:"Hoa\\Ruler\\Model\\Model":1:{s:8:"' . "\0" . '*' . "\0" . '_root";O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:">";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:6:"points";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:0:{}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";i:30;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;}}';

        return unserialize($rule);
    }

    private function getCustomOperatorRule()
    {
        // serialized rule for "points > 30 and always_true()"
        $rule = 'O:21:"Hoa\\Ruler\\Model\\Model":1:{s:8:"' . "\0" . '*' . "\0" . '_root";O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:3:"and";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:">";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:6:"points";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:0:{}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";i:30;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;}i:1;O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:11:"always_true";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:0:{}s:12:"' . "\0" . '*' . "\0" . '_function";b:1;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;}}';

        return unserialize($rule);
    }
}
