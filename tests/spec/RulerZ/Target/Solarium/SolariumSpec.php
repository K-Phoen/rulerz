<?php

namespace spec\RulerZ\Target\Solarium;

use PhpSpec\Exception\Example\SkippingException;
use Solarium\Client as SolariumClient;

use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;
use spec\RulerZ\Target\BaseTargetBehavior;

/**
 * TODO: refactor. It currently tests both the Solarium and Solariumisitor classes.
 */
class SolariumSpec extends BaseTargetBehavior
{
    function it_supports_satisfies_mode_with_a_solarium_client(SolariumClient $client)
    {
        $this->supports($client, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_filter_mode_with_a_solarium_client(SolariumClient $client)
    {
        $this->supports($client, CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_return_an_executor_model()
    {
        $rule = 'points = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->shouldHaveType('RulerZ\Model\Executor');

        $executorModel->getTraits()->shouldHaveCount(2);
        $executorModel->getCompiledRule()->shouldReturn("'points:1'");
    }

    function it_supports_parameters()
    {
        $rule = 'points > :nb_points and group IN [:admin_group, :super_admin_group]';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('\'(points:{\'. $parameters[\'nb_points\'] .\' TO *] AND group:(\'. $parameters[\'admin_group\'] .\' OR \'. $parameters[\'super_admin_group\'] .\'))\'');
    }

    function it_supports_custom_operators()
    {
        throw new SkippingException('Not yet implemented.');

        $rule = 'points > 30 and always_true()';

        $this->defineOperator('always_true', function () {
            throw new \LogicException('should not be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('\'(points:{30 TO *] AND \'.call_user_func($operators["always_true"])).\'\'');
    }

    function it_supports_custom_inline_operators()
    {
        $rule = 'points > 30 and always_true()';

        $this->defineInlineOperator('always_true', function () {
            return '1 = 1';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn("'(points:{30 TO *] AND 1 = 1)'");
    }
}
