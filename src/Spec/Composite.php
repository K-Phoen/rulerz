<?php

namespace RulerZ\Spec;

use RulerZ\Exception\ParameterOverridenException;

class Composite implements Specification
{
    /**
     * @var string $operator
     */
    private $operator;

    /**
     * @var array
     */
    private $specifications = [];

    /**
     * Builds a composite specification.
     *
     * @param string $operator       The operator used to join the specifications.
     * @param array  $specifications A list of initial specifications.
     */
    public function __construct($operator, array $specifications = [])
    {
        $this->operator = $operator;

        foreach ($specifications as $specification) {
            $this->addSpecification($specification);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRule()
    {
        return implode(sprintf(' %s ', $this->operator), array_map(function (Specification $specification) {
            return $specification->getRule();
        }, $this->specifications));
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        $parametersCount = 0;

        $parametersList = array_map(function (Specification $specification) use (&$parametersCount) {
            $parametersCount += count($specification->getParameters());

            return $specification->getParameters();
        }, $this->specifications);

        $mergedParameters = call_user_func_array('array_merge', $parametersList);

        // error handling in case of overriden parameters
        if ($parametersCount !== count($mergedParameters)) {
            $overridenParameters = $this->searchOverridenParameters($parametersList);
            $specificationsTypes = array_map(function(Specification $spec) {
                return get_class($spec);
            }, $this->specifications);

            throw new ParameterOverridenException(sprintf(
                'Looks like some parameters were overriden (%s) while combining specifications of types %s' . "\n" .
                'More information on how to solve this can be found here: https://github.com/K-Phoen/rulerz/issues/3',
                implode(', ', $overridenParameters),
                implode(', ', $specificationsTypes)
            ));
        }

        return $mergedParameters;
    }

    /**
     * Adds a new specification.
     *
     * @param Specification $specification
     */
    public function addSpecification(Specification $specification)
    {
        $this->specifications[] = $specification;
    }

    /**
     * Search the parameters that were overriden during the parameters-merge phase.
     *
     * @param array $parametersList
     *
     * @return array Names of the overriden parameters.
     */
    private function searchOverridenParameters(array $parametersList)
    {
        $parametersUsageCount = [];

        foreach ($parametersList as $list) {
            foreach (array_keys($list) as $parameter) {
                if (!isset($parametersUsageCount[$parameter])) {
                    $parametersUsageCount[$parameter] = 0;
                }

                $parametersUsageCount[$parameter] += 1;
            }
        }

        $overridenParameters = array_filter($parametersUsageCount, function($count) {
            return $count > 1;
        });

        return array_keys($overridenParameters);
    }
}
