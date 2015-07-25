<?php

namespace RulerZ\Parser;

use Doctrine\Common\Cache\Cache;

/**
 * Caches rules.
 */
class CachedParser implements Parser
{
    /**
     * @var Parser $parser
     */
    private $parser;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var integer $lifeTime
     */
    private $lifeTime;

    /**
     * @param Parser $wrappedParser The parser to cache.
     * @param Cache  $cache         The cache provider to use.
     * @param int    $lifeTime      The lifetime of a cached rule (0 to disable).
     */
    public function __construct(Parser $wrappedParser, Cache $cache, $lifeTime = 0)
    {
        $this->parser   = $wrappedParser;
        $this->cache    = $cache;
        $this->lifeTime = $lifeTime;
    }

    /**
     * {@inheritDoc}
     */
    public function parse($rule)
    {
        if (($cache = $this->cache->fetch($rule)) !== false) {
            return unserialize($cache);
        }

        $ast = $this->parser->parse($rule);

        $this->cache->save($rule, serialize($ast), $this->lifeTime);

        return $ast;
    }
}
