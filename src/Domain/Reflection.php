<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use ReflectionClass;

/**
 * @internal
 */
final class Reflection
{
    /**
     * @var object
     */
    private $instance;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * Creates an new instance of Reflection.
     *
     * @param  object  $instance
     */
    public function __construct(object $instance)
    {
        $this->instance = $instance;
        $this->reflectionClass = new ReflectionClass($instance);
    }

    /**
     * Sets an private attribute value on the given instance.
     *
     * @param  object  $instance
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return $this
     */
    public function set(string $attribute, $value): Reflection
    {
        $property = $this->reflectionClass->getProperty($attribute);
        $property->setAccessible(true);
        $property->setValue($this->instance, $value);

        return $this;
    }
}
