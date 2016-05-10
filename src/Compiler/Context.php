<?php

namespace RulerZ\Compiler;

/**
 * Simple container that will hold compilation-related information.
 *
 * Usage:
 *
 * ```
 * $context = new Context();
 * $context['es_host'] = 'localhost';
 * $context['es_port'] = 9200;
 * ```
 */
class Context extends \ArrayObject
{
}
