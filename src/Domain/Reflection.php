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
    private object $instance;

    private ReflectionClass $reflectionClass;

    /**
     * Creates an new instance of Reflection.
     */
    public function __construct(object $instance)
    {
        $this->instance = $instance;
        $this->reflectionClass = new ReflectionClass($instance);
    }

    /**
     * Sets an private attribute value on the given instance.
     */
    public function set(string $attribute, mixed $value): Reflection
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
     * Gets an private attribute value on the given instance.
     */
    public function get(string $attribute): mixed
    {
        $property = $this->reflectionClass->getProperty($attribute);

        $property->setAccessible(true);

        return $property->getValue($this->instance);
    }

    /**
     * @throws ReflectionException
     */
    private static function setProperty(
        ReflectionClass $class,
        mixed $instance,
        string $attribute,
        mixed $value
    ): void {
        try {
            $property = $class->getProperty($attribute);
            $property->setAccessible(true);
            $property->setValue($instance, $value);
        } catch (ReflectionException $exception) {
            $parentClass = $class->getParentClass();

            if ($parentClass === false) {
                throw $exception;
            }

            self::setProperty(
                $parentClass,
                $instance,
                $attribute,
                $value
            );
        }
    }
}
