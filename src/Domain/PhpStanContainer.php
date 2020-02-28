<?php

namespace NunoMaduro\PhpInsights\Domain;

use _HumbugBox075db77467cb\Nette\DI\MissingServiceException;
use PHPStan\Rules\Rule;
use Psr\Container\ContainerInterface;

class PhpStanContainer implements ContainerInterface
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

    public function getParameter(string $parameterName)
    {
        return $this->container->getParameter($parameterName);
    }

    public function createRuleWithArgs(string $ruleClass, array $args): Rule
    {

    }
}
