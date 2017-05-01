<?php

namespace spec\RulerZ\Target\Elasticsearch;

use Elasticsearch\Client;

use RulerZ\Compiler\CompilationTarget;
use spec\RulerZ\Target\BaseTargetBehavior;
use spec\RulerZ\Target\ElasticsearchVisitorExamples;

/**
 * TODO: refactor. It currently tests both the Elasticsearch and GenericElasticsearchVisitor classes.
 */
class ElasticsearchSpec extends BaseTargetBehavior
{
    use ElasticsearchVisitorExamples;

    public function it_supports_satisfies_mode_with_a_client(Client $client)
    {
        $this->supports($client, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    public function it_supports_filter_mode_with_a_client(Client $client)
    {
        $this->supports($client, CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }
}
