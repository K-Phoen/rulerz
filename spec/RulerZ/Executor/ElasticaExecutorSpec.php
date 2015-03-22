<?php

namespace spec\RulerZ\Executor;

use Elastica\Search;
use Elastica\SearchableInterface;
use PhpSpec\ObjectBehavior;

use RulerZ\Executor\Executor;

class ElasticaExecutorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Executor\ElasticaExecutor');
    }

    function it_supports_satisfies_mode_with_a_search(Search $search)
    {
        $this->supports($search, Executor::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_satisfies_mode_with_a_searchable_object(SearchableInterface $search)
    {
        $this->supports($search, Executor::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_using_a_search(Search $search)
    {
        $this->supports($search, Executor::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_filter_using_a_searchable_object(SearchableInterface $search)
    {
        $this->supports($search, Executor::MODE_FILTER)->shouldReturn(true);
    }

    /**
     * @dataProvider unsupportedTypes
     */
    function it_can_not_filter_other_types($type)
    {
        $this->supports($type, Executor::MODE_FILTER)->shouldReturn(false);
    }

    function it_can_filter_with_a_rule(Search $search)
    {
        $expectedQuery = [
            'bool' => [
                'must' => [
                    'range' => ['points' => ['gt' => 30]],
                ]
            ]
        ];

        $search->search(['query' => $expectedQuery])->willReturn('result');

        $this->filter($search, $this->getSimpleRule())->shouldReturn('result');
    }

    function it_throws_an_exception_when_calling_an_unknown_operator(Search $search)
    {
        $this
            ->shouldThrow('RulerZ\Exception\OperatorNotFoundException')
            ->duringFilter($search, $this->getCustomOperatorRule());
    }

    public function unsupportedTypes()
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
