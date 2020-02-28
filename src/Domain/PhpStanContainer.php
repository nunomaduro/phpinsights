<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use _HumbugBox075db77467cb\Nette\DI\MissingServiceException;
use Psr\Container\ContainerInterface;

final class PhpStanContainer implements ContainerInterface
{
    /** @var \PHPStan\DependencyInjection\Container */
    private $container;

    /**
     * PhpStanContainer constructor.
     *
     * @param \PHPStan\DependencyInjection\Container $container
     */
    public function __construct(\PHPStan\DependencyInjection\Container $container)
    {
        $this->container = $container;
    }

    public function get($id)
    {
        return $this->container->getByType($id);
    }

    public function has($id)
    {
        try {
            $this->container->getByType($id);
            return true;
        } catch (MissingServiceException $exception) {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getParameter(string $parameterName)
    {
        return $this->container->getParameter($parameterName);
    }
}
