<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Infrastructure\FileProcessors;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FileProcessor;
use NunoMaduro\PhpInsights\Domain\PhpStanRulesRegistry;
use PhpParser\Node;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Node\FileNode;
use PHPStan\Parser\Parser;
use Symfony\Component\Finder\SplFileInfo;

final class PhpStanFileProcessor implements FileProcessor
{
    /** @var \PHPStan\Analyser\ScopeFactory */
    private $scopeFactory;

    /** @var \NunoMaduro\PhpInsights\Domain\PhpStanRulesRegistry */
    private $registry;

    /** @var \PHPStan\Parser\Parser */
    private $parser;

    /** @var NodeScopeResolver */
    private $nodeScopeResolver;

    /**
     * PhpStanFileProcessor constructor.
     *
     * @param \PHPStan\Analyser\ScopeFactory $scopeFactory
     * @param \PHPStan\Parser\Parser $parser
     * @param \PHPStan\Analyser\NodeScopeResolver $nodeScopeResolver
     */
    public function __construct(ScopeFactory $scopeFactory,
                                Parser $parser,
                                NodeScopeResolver $nodeScopeResolver)
    {
        $this->scopeFactory = $scopeFactory;
        $this->parser = $parser;
        $this->nodeScopeResolver = $nodeScopeResolver;
        $this->registry = new PhpStanRulesRegistry([]);
    }

    /**
     * @param array<\NunoMaduro\PhpInsights\Domain\Insights\RuleDecorator> $rules
     *
     * @throws \ReflectionException
     */
    public function addRules(array $rules): void
    {
        $this->registry->addRules($rules);
    }

    public function processFile(SplFileInfo $splFileInfo): void
    {
        $path = $splFileInfo->getRealPath();

        if ($path === false) {
            return;
        }

        $scope = $this->scopeFactory->create(ScopeContext::create($path));
        $node = $this->parser->parseFile($path);

        $this->processNode(new FileNode($node), $scope);

        // We load the file as phpStan needs it loaded to parse
        // it correctly. (only needed when autoloader fails to load it)
        spl_autoload_register($autoloader = static function () use ($path): void {
            require_once $path;
        });

        $this->nodeScopeResolver->processNodes(
            $node,
            $scope,
            function (Node $node, Scope $scope): void {
                $this->processNode($node, $scope);
            }
        );

        spl_autoload_unregister($autoloader);
    }

    private function processNode(Node $node, Scope $scope): void
    {
        foreach ($this->registry->getRules(get_class($node)) as $rule) {
            $rule->processNode($node, $scope);
        }
    }
}
