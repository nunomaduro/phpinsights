<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\PhpStanRuleDecorator;
use NunoMaduro\PhpInsights\Domain\PhpStanRulesRegistry;
use PhpParser\Node;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Node\FileNode;
use PHPStan\Parser\Parser;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

final class PhpStanRuleFileProcessor implements FileProcessor
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

    public function support(Insight $insight): bool
    {
        return $insight instanceof PhpStanRuleDecorator;
    }

    public function addChecker(Insight $insight): void
    {
        if (! $insight instanceof PhpStanRuleDecorator) {
            throw new RuntimeException(sprintf(
                'Unable to add %s, not a PhpStan Rule instance',
                get_class($insight)
            ));
        }

        $this->registry->addRules([$insight]);
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

        $this->nodeScopeResolver->processNodes(
            $node,
            $scope,
            function (Node $node, Scope $scope): void {
                $this->processNode($node, $scope);
            }
        );
    }

    private function processNode(Node $node, Scope $scope): void
    {
        foreach ($this->registry->getRules(get_class($node)) as $insight) {
            $insight->processNode($node, $scope);
        }
    }
}
