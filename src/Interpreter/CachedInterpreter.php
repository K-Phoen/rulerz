<?php

namespace RulerZ\Interpreter;

use Doctrine\Common\Cache\Cache;

/**
 * Caches rules.
 */
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

    /**
     * @var integer $lifeTime
     */
    private $lifeTime;

    /**
     * @param Interpreter $wrappedInterpreter The interpreter to cache.
     * @param Cache       $cache              The cache provider to use.
     * @param int         $lifeTime           The lifetime of a cached rule (0 to disable).
     */
    public function __construct(Interpreter $wrappedInterpreter, Cache $cache, $lifeTime = 0)
    {
        $this->interpreter = $wrappedInterpreter;
        $this->cache = $cache;
        $this->lifeTime = $lifeTime;
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

        $this->cache->save($rule, serialize($ast), $this->lifeTime);

        return $ast;
    }
}
