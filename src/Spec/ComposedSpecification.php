<?php

declare(strict_types=1);

namespace RulerZ\Spec;

/**
 * Used to compose specification in a single class.
 *
 * Sample usage:
 *
 * ```
 * class AccessibleBy extends \RulerZ\Spec\ComposedSpecification
 * {
 *     private $user;
 *
 *     public function __construct(Entity\User $user)
 *     {
 *         $this->user = $user;
 *     }
 *
 *     protected function getSpecification()
 *     {
 *         return (new IsOwner($this->user))->orX(new IsMember($this->user));
 *     }
 * }
 * ```
 */
abstract class ComposedSpecification extends AbstractSpecification
{
    private $specification;

    /**
     * The composed specification.
     *
     * @return Specification
     */
    abstract protected function getSpecification();

    /**
     * {@inheritdoc}
     */
    public function getRule(): string
    {
        $spec = $this->initializeSpecification();

        return $spec->getRule();
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        $spec = $this->initializeSpecification();

        return $spec->getParameters();
    }

    private function initializeSpecification()
    {
        if ($this->specification === null) {
            $this->specification = $this->getSpecification();
        }

        return $this->specification;
    }
}
