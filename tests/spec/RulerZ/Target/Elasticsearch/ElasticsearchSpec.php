<?php

namespace spec\RulerZ\Target\Elasticsearch;

use Elasticsearch\Client;
use PhpSpec\ObjectBehavior;

use RulerZ\Compiler\CompilationTarget;
use spec\RulerZ\Target\ElasticsearchVisitorExamples;

/**
 * TODO: refactor. It currently tests both the Elasticsearch and GenericElasticsearchVisitor classes.
 */
class ElasticsearchSpec extends ObjectBehavior
{
    use ElasticsearchVisitorExamples;

    function it_supports_satisfies_mode_with_a_client(Client $client)
    {
        $this->supports($client, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_filter_mode_with_a_client(Client $client)
    {
        $this->supports($client, CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    /**
     * @dataProvider unsupportedTypes
     */
    function it_can_not_filter_other_types($type)
    {
        $this->supports($type, CompilationTarget::MODE_FILTER)->shouldReturn(false);
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
}
