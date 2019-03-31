<?php

namespace Tests;

use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $applicationContainer;

    /**
     * Creates a new instance of the Test Container.
     */
    private function __construct()
    {
        $this->applicationContainer = require __DIR__.'/../config/container.php';
    }

    /**
     * Resolves the given id from the container.
     *
     * @param  string $id
     *
     * @return mixed
     */
    public static function resolve(string $id)
    {
        return (new self())->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->applicationContainer->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id): bool
    {
        return $this->applicationContainer->has($id);
    }
}
