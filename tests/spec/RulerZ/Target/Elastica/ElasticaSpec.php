<?php

namespace spec\RulerZ\Target\Elastica;

use Elastica\Search;
use Elastica\SearchableInterface;

use RulerZ\Compiler\CompilationTarget;
use spec\RulerZ\Target\BaseTargetBehavior;
use spec\RulerZ\Target\ElasticsearchVisitorExamples;

/**
 * TODO: refactor. It currently tests both the Elastica and GenericElasticsearchVisitor classes.
 */
class ElasticaSpec extends BaseTargetBehavior
{
    use ElasticsearchVisitorExamples;

    function it_supports_satisfies_mode_with_a_search(Search $search)
    {
        $this->supports($search, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_filter_mode_with_a_search(Search $search)
    {
        $this->supports($search, CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    function it_supports_satisfies_mode_with_a_search_object(SearchableInterface $search)
    {
        $this->supports($search, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_filter_mode_with_a_search_object(SearchableInterface $search)
    {
        $this->supports($search, CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }
}
