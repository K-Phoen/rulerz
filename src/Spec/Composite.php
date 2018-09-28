<?php

declare(strict_types=1);

namespace RulerZ\Spec;

use RulerZ\Exception\ParameterOverridenException;

/**
 * @internal
 */
class Composite implements Specification
{
    /**
     * @var string
     */
    private $operator;

    /**
     * @var array
     */
    private $specifications = [];

    /**
     * Builds a composite specification.
     *
     * @param string $operator The operator used to join the specifications.
     * @param array $specifications A list specifications to combine.
     */
    public function __construct($operator, array $specifications = [])
    {
        $this->operator = $operator;

        if (empty($specifications)) {
            throw new \LogicException('No specifications given.');
        }

        foreach ($specifications as $specification) {
            $this->addSpecification($specification);
        }
    }

    private function addSpecification(Specification $specification): void
    {
        $this->specifications[] = $specification;
    }

    /**
     * {@inheritdoc}
     */
    public function getRule(): string
    {
        return implode(sprintf(' %s ', $this->operator), array_map(function (Specification $specification) {
            return sprintf('(%s)', $specification->getRule());
        }, $this->specifications));
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        $parametersCount = 0;

        $parametersList = array_map(function (Specification $specification) use (&$parametersCount) {
            $parametersCount += count($specification->getParameters());

            return $specification->getParameters();
        }, $this->specifications);

        $mergedParameters = array_merge([], ...$parametersList);

        // error handling in case of overriden parameters
        if ($parametersCount !== count($mergedParameters)) {
            $overridenParameters = $this->searchOverridenParameters($parametersList);
            $specificationsTypes = array_map(function (Specification $spec) {
                return get_class($spec);
            }, $this->specifications);

            throw new ParameterOverridenException(sprintf(
                'Looks like some parameters were overriden (%s) while combining specifications of types %s'."\n".
                'More information on how to solve this can be found here: https://github.com/K-Phoen/rulerz/issues/3',
                implode(', ', $overridenParameters),
                implode(', ', $specificationsTypes)
            ));
        }

        return $mergedParameters;
    }

    /**
     * Search the parameters that were overridden during the parameters-merge phase.
     *
     * @return array Names of the overridden parameters.
     */
    private function searchOverridenParameters(array $parametersList): array
    {
        $parametersUsageCount = [];

        foreach ($parametersList as $list) {
            foreach ($list as $parameter => $_value) {
                if (!isset($parametersUsageCount[$parameter])) {
                    $parametersUsageCount[$parameter] = 0;
                }

                $parametersUsageCount[$parameter] += 1;
            }
        }

        $overriddenParameters = array_filter($parametersUsageCount, function ($count) {
            return $count > 1;
        });

        return array_keys($overriddenParameters);
    }
}
