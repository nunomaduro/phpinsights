<?php

namespace NunoMaduro\PhpInsights\Infrastructure\FileProcessors;

use NunoMaduro\PhpInsights\Domain\PhpStanRulesRegistry;
use PhpParser\Node;
use PHPStan\Analyser\Analyser;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Node\FileNode;
use PHPStan\Parser\Parser;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

class PhpStanFileProcessor implements FileProcessorInterface
{
    /**
     * @var \PHPStan\Analyser\Analyser
     */
    private $analyser;

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
     */
    public function __construct(ScopeFactory $scopeFactory, Parser $parser, NodeScopeResolver $nodeScopeResolver)
    {
        $this->scopeFactory = $scopeFactory;
        $this->parser = $parser;
        $this->nodeScopeResolver = $nodeScopeResolver;
        $this->registry = new PhpStanRulesRegistry([]);
    }

    public function getCheckers(): array
    {
        return ["filler"];
    }

    /**
     * @param array<\PHPStan\Rules\Rule> $rules
     */
    public function addRules(array $rules): void
    {
        $this->registry->addRules($rules);
    }

    /**
     * @param \Symplify\PackageBuilder\FileSystem\SmartFileInfo $smartFileInfo
     *
     * @return string
     *
     * @throws \Throwable
     */
    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $path = $smartFileInfo->getRealPath();

        if ($path === false) {
            return "Failed getting real path of file.";
        }

        $scope = $this->scopeFactory->create(ScopeContext::create($path));
        $node = $this->parser->parseFile($path);

        $this->processNode(new FileNode($node), $scope);

        $this->nodeScopeResolver->processNodes(
            $node,
            $scope,
            function (Node $node, Scope $scope) {
                $this->processNode($node, $scope);
            }
        );

        return "Processed";
    }

    private function processNode(Node $node, Scope $scope)
    {
        foreach ($this->registry->getRules(get_class($node)) as $rule) {
            $rule->processNode($node, $scope);
        }
    }
}
