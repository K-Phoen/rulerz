<?php

namespace spec\RulerZ\Executor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Executor\Executor;
use RulerZ\Stub\ModelStub;

class PommExecutorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Executor\PommExecutor');
    }

    function it_supports_satisfies_mode()
    {
        $this->supports(new ModelStub(), Executor::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_where_clauses()
    {
        $this->supports(new ModelStub(), Executor::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_not_filter_other_types()
    {
        foreach ($this->unsupportedTypes() as $type) {
            $this->supports($type, Executor::MODE_FILTER)->shouldReturn(false);
        }
    }

    function it_can_filter_a_clause_with_a_rule(ModelStub $query)
    {
        $query->findWhere(Argument::that(function($where) {
            return (string) $where === 'points > 30';
        }))->willReturn('result');

        $this->filter($query, $this->getSimpleRule())->shouldReturn('result');
    }

    function it_supports_custom_operators(ModelStub $query)
    {
        $this->registerOperators([
            'always_true' => function() {
                return '1 = 1';
            }
        ]);

        $query->findWhere(Argument::that(function($where) {
            return (string) $where === '(points > 30 AND 1 = 1)';
        }))->willReturn('result');

        $this->filter($query, $this->getCustomOperatorRule())->shouldReturn('result');
    }

    function it_implicitly_converts_unknown_operators(ModelStub $query)
    {
        $query->findWhere(Argument::that(function($where) {
            return (string) $where === '(points > 30 AND always_true())';
        }))->willReturn('result');

        $this->filter($query, $this->getCustomOperatorRule())->shouldReturn('result');
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
