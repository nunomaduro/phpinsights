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
     * @param object $instance
     */
    public function __construct(object $instance)
    {
        $this->instance = $instance;
        $this->reflectionClass = new ReflectionClass($instance);
    }

    /**
     * Sets an private attribute value on the given instance.
     *
     * @param string $attribute
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
     * @param mixed $instance
     * @param string $attribute
     * @param mixed $value
     *
     * @throws ReflectionException
     */
    private static function setProperty(
        ReflectionClass $class,
        $instance,
        string $attribute,
        $value
    ): void
    {
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

    /**
     * Gets an private attribute value on the given instance.
     *
     * @param string $attribute
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function get(string $attribute)
    {
        return self::getProperty(
            $this->reflectionClass,
            $this->instance,
            $attribute
        );
    }

    /**
     * @param ReflectionClass $class
     * @param mixed $instance
     * @param string $attribute
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    private static function getProperty(
        ReflectionClass $class,
        $instance,
        string $attribute
    )
    {
        try {
            $property = $class->getProperty($attribute);
            $property->setAccessible(true);

            return $property->getValue($instance);
        } catch (ReflectionException $exception) {
            $parentClass = $class->getParentClass();

            if ($parentClass === false) {
                throw $exception;
            }

            return self::getProperty(
                $parentClass,
                $instance,
                $attribute
            );
        }
    }
}
