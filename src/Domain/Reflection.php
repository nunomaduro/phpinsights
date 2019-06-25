<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use ReflectionClass;
use ReflectionException;

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
    public function __construct($instance)
    {
        $this->instance = $instance;
        $this->reflectionClass = new ReflectionClass($instance);
    }

    /**
     * Sets an private attribute value on the given instance.
     *
     * @param  string  $attribute
     * @param mixed $value
     *
     * @return \NunoMaduro\PhpInsights\Domain\Reflection
     */
    public function set(string $attribute, $value): Reflection
    {
        self::setProperty(
            $this->reflectionClass,
            $this->instance,
            $attribute,
            $value
        );

        return $this;
    }

    /**
     * @param ReflectionClass $class
     * @param                 $instance
     * @param string          $attribute
     * @param                 $value
     */
    private static function setProperty(
        ReflectionClass $class,
        $instance,
        string $attribute,
        $value
    ) {
        try {
            $property = $class->getProperty($attribute);
            $property->setAccessible(true);
            $property->setValue($instance, $value);
        } catch (ReflectionException $exception) {
            self::setProperty(
                $class->getParentClass(),
                $instance,
                $attribute,
                $value
            );
        }
    }

    /**
     * Gets an private attribute value on the given instance.
     *
     * @param  string  $attribute
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
