<?php

namespace RulerZ\Visitor\Polyfill;

use RulerZ\Exception\ParameterNotFoundException;

trait Parameters
{
    /**
     * List of parameters.
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Define the parameters to be used.
     *
     * @param array $parameters A key/value list of parameters.
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Return the value of a parameter.
     *
     * @param string $name The parameter's name.
     *
     * @return mixed
     */
    protected function lookupParameter($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new ParameterNotFoundException(sprintf('Parameter "%s" not defined', $name));
        }

        return $this->parameters[$name];
    }
}
