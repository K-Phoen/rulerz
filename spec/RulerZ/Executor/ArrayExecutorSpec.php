<?php

namespace spec\RulerZ\Executor;

use PhpSpec\ObjectBehavior;

use RulerZ\Executor\Executor;

class ArrayExecutorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Executor\ArrayExecutor');
    }

    function it_supports_satisfies_mode()
    {
        $this->supports([], Executor::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_filtering_arrays()
    {
        $this->supports([], Executor::MODE_FILTER)->shouldReturn(true);
    }

    function it_does_not_support_filtering_other_types()
    {
        foreach ($this->unsupportedTypes() as $type) {
            $this->supports($type, Executor::MODE_FILTER)->shouldReturn(false);
        }
    }

    function it_supports_satisfaction_tests_for_arrays()
    {
        $this->supports([], Executor::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_an_array_with_a_rule()
    {
        $this->filter($this->getTarget(), $this->getSimpleRule())->shouldReturn($this->getResult());
    }

    function it_can_tell_if_a_target_satisfies_a_rule()
    {
        $this->satisfies($this->getTarget()[0], $this->getSimpleRule())->shouldReturn(true);
    }

    function it_can_filter_an_array_of_objects()
    {
        $target = array_map(function($row) {
            return (object) $row;
        }, $this->getTarget());
        $result = array_map(function($row) {
            return (object) $row;
        }, $this->getResult());

        $this->filter($target, $this->getSimpleRule())->shouldBeLike($result);
    }

    function it_supports_complex_targets()
    {
        $this->satisfies($this->getComplexTarget(), $this->getComplexRule())->shouldReturn(true);
    }

    function it_supports_custom_operators()
    {
        $this->registerOperators([
            'always_true' => function() {
                return true;
            }
        ]);

        $this->filter($this->getTarget(), $this->getCustomOperatorRule())->shouldReturn($this->getResult());
    }

    function it_throws_an_exception_when_calling_an_unknown_operator()
    {
        $this
            ->shouldThrow('RulerZ\Exception\OperatorNotFoundException')
            ->duringFilter($this->getTarget(), $this->getCustomOperatorRule());
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

    private function getTarget()
    {
        return [
            ['name' => 'Joe', 'points' => 40],
            ['name' => 'Moe', 'points' => 20],
        ];
    }

    private function getComplexTarget()
    {
        return [
            'name'  => 'Joe',
            'stats' => (object) [
                'score' => 50
            ]
        ];
    }

    public function getComplexRule()
    {
        // serialized rule for "stats.score > 40"
        $rule = 'O:21:"Hoa\\Ruler\\Model\\Model":1:{s:8:"' . "\0" . '*' . "\0" . '_root";O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:">";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:5:"stats";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:1:{i:0;a:2:{i:0;i:1;i:1;s:5:"score";}}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";i:40;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;}}';

        return unserialize($rule);
    }

    private function getResult()
    {
        return [
            ['name' => 'Joe', 'points' => 40],
        ];
    }

    private function unsupportedTypes()
    {
        return [
            'string',
            42,
            new \stdClass,
        ];
    }
}
