<?php

namespace Interpreter;

use Doctrine\Common\Cache\Cache;

class CachedInterpreter implements Interpreter
{
    /**
     * @var Interpreter $interpreter
     */
    private $interpreter;

    /**
     * @var Cache $cache
     */
    private $cache;

    public function __construct(Interpreter $wrappedInterpreter, Cache $cache)
    {
        $this->interpreter = $wrappedInterpreter;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function interpret($rule)
    {
        if ($this->cache->contains($rule)) {
            return unserialize($this->cache->fetch($rule));
        }

        $ast = $this->interpreter->interpret($rule);

        $this->cache->save($rule, serialize($ast));

        return $ast;
    }
}
