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
     * @param object $instance
     */
    public function __construct($instance)
    {
        $this->instance = $instance;
        $this->reflectionClass = new ReflectionClass($instance);
    }

    /**
     * Sets an private attribute value on the given instance.
     *
     * @param string        $attribute
     * @param string[]|bool $value
     *
     * @return \NunoMaduro\PhpInsights\Domain\Reflection
     */
    public function set(string $attribute, $value): Reflection
    {
        $property = $this->reflectionClass->getProperty($attribute);
        $property->setAccessible(true);
        $property->setValue($this->instance, $value);

        return $this;
    }

    /**
     * Gets an private attribute value on the given instance.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    public function get(string $attribute)
    {
        $property = $this->reflectionClass->getProperty($attribute);

        $property->setAccessible(true);

        return $property->getValue($this->instance);
    }
}
