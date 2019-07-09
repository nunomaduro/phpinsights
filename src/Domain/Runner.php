<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Sniffs\SniffDecorator;
use PHPStan\Rules\Registry;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Finder\SourceFinder;

/**
 * @internal
 */
final class Runner
{
    /** @var \Symfony\Component\DependencyInjection\Container */
    private $ecsContainer;

    /** @var \Nette\DI\Container */
    private $phpStanContainer;

    /** @var \NunoMaduro\PhpInsights\Domain\FileProcessor */
    private $fileProcessor;

    /** @var string */
    private $baseDir;

    /**
     * InsightContainer constructor.
     *
     * @param string $baseDir
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository $filesRepository
     *
     * @throws \Exception
     */
    public function __construct(
        string $baseDir,
        OutputInterface $output,
        FilesRepository $filesRepository
    )
    {
        $reflection = new Reflection($configuration = new Configuration());
        $reflection->set('sources', [$baseDir])
            ->set('shouldClearCache', true)
            ->set('showProgressBar', true);

        $ecsContainer = $this->ecsContainer = EcsContainer::make();

        $ecsContainer->set(Configuration::class, $configuration);
        /** @var EasyCodingStandardStyle $style */
        $style = $ecsContainer->get(EasyCodingStandardStyle::class);
        (new Reflection($style))->set('output', $output);

        /** @var \Symplify\EasyCodingStandard\Finder\SourceFinder $sourceFinder */
        $sourceFinder = $ecsContainer->get(SourceFinder::class);
        $sourceFinder->setCustomSourceProvider($filesRepository);

        $this->phpStanContainer = PhpStanContainer::make($baseDir);

        $this->fileProcessor = Container::make()->get(FileProcessor::class);
        $this->baseDir = $baseDir;

        $this->addFileProcessor($this->fileProcessor);

        $analyser = $this->phpStanContainer->getByType(\PHPStan\Analyser\Analyser::class);
        $this->fileProcessor->setAnalyser($analyser);
    }

    public function getEcsApplication(): EasyCodingStandardApplication
    {
        return $this->ecsContainer->get(EasyCodingStandardApplication::class);
    }

    public function getSniffErrorCollector(): ErrorAndDiffCollector
    {
        return $this->ecsContainer->get(ErrorAndDiffCollector::class);
    }

    public function reset(): void
    {
        $this->ecsContainer->reset();
    }

    /**
     * @param array<\PHPStan\Rules\Rule> $rules
     */
    public function addRules(array $rules): void
    {
        $this->phpStanContainer->removeService('registry');
        $this->phpStanContainer->addService(
            'registry',
            new Registry($rules)
        );
    }

    /**
     * @param array<\PHP_CodeSniffer\Sniffs\Sniff> $sniffs
     */
    public function addSniffs(array $sniffs): void
    {
        foreach ($sniffs as $sniff) {
            $this->fileProcessor->addSniff(new SniffDecorator($sniff, $this->baseDir));
        }
    }

    private function addFileProcessor(FileProcessor $fileProcessor): void
    {
        $application = $this->getEcsApplication();
        $reflection = new Reflection($application);

        /** @var \Symplify\EasyCodingStandard\Contract\Application\FileProcessorCollectorInterface $fileProcessorCollector */
        $fileProcessorCollector = $reflection->set('fileProcessors', [])
            ->get('singleFileProcessor');

        $fileProcessorCollector->addFileProcessor($fileProcessor);
        $application->addFileProcessor($fileProcessor);
    }

    public function run(): void
    {
        $this->getEcsApplication()->run();
    }
}
