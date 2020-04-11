<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\FileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\DetailsComparator;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes as ArchitectureClasses;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Files;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Globally as ArchitectureGlobally;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Interfaces as ArchitectureInterfaces;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Traits as ArchitectureTraits;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Globally;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity;
use NunoMaduro\PhpInsights\Domain\Results;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

/**
 * @internal
 *
 * @see \Tests\Application\Console\Formatters\ConsoleTest
 */
final class Console implements Formatter
{
    private const BLOCK_SIZE = 9;
    private const ALL_BLOCKS_IN_ROW = 4;
    private const TWO_BLOCKS_IN_ROW = 2;
    private const MIN_SPACEWIDTH = 5;
    private const MAX_SPACEWIDTH = 15;

    private const SUBTITLE = 'fg=white;options=bold;fg=white';

    private const QUALITY = <<<EOD
    <%quality_color%>%block_size%</>
    <fg=black;options=bold;%quality_color%>  %quality%  </>
    <%quality_color%>%block_size%</>
    EOD;

    private const COMPLEXITY = <<<EOD
    <%complexity_color%>%block_size%</>
    <fg=black;options=bold;%complexity_color%>  %complexity%  </>
    <%complexity_color%>%block_size%</>
    EOD;

    private const STRUCTURE = <<<EOD
    <%structure_color%>%block_size%</>
    <fg=black;options=bold;%structure_color%>  %structure%  </>
    <%structure_color%>%block_size%</>
    EOD;

    private const STYLE = <<<EOD
    <%style_color%>%block_size%</>
    <fg=black;options=bold;%style_color%>  %style%  </>
    <%style_color%>%block_size%</>
    EOD;

    private const CATEGORY_COLOR = [
        'Code' => 'cyan',
        'Complexity' => 'green',
        'Architecture' => 'blue',
        'Style' => 'yellow',
        'Security' => 'red',
    ];

    private const CODE_METRIC_CLASSES = [
        Comments::class,
        Classes::class,
        Functions::class,
        Globally::class,
    ];

    private const ARCHITECTURE_METRIC_CLASSES = [
        ArchitectureClasses::class,
        ArchitectureInterfaces::class,
        ArchitectureGlobally::class,
        ArchitectureTraits::class,
    ];

    private Style $style;

    private int $totalWidth;

    private FileLinkFormatter $fileLinkFormatter;

    private bool $supportHyperLinks;

    private Configuration $config;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->style = new Style($input, $output);
        $this->totalWidth = (new Terminal())->getWidth();

        $outputFormatterStyle = new OutputFormatterStyle();
        $this->config = Container::make()->get(Configuration::class);

        $this->fileLinkFormatter = $this->config->getFileLinkFormatter();
        $this->supportHyperLinks = method_exists($outputFormatterStyle, 'setHref');
    }

    /**
     * Format the result to the desired format.
     *
     * @param array<int, string> $metrics
     */
    public function format(InsightCollection $insightCollection, array $metrics): void
    {
        $results = $insightCollection->results();

        $this->summary($results, $insightCollection->getCollector()->getAnalysedPaths())
            ->code($insightCollection, $results)
            ->complexity($insightCollection, $results)
            ->architecture($insightCollection, $results)
            ->miscellaneous($results);

        $this->issues($insightCollection, $metrics, $insightCollection->getCollector()->getCommonPath());

        if ($this->config->hasFixEnabled()) {
            $this->formatFix($insightCollection, $metrics);
        }
    }

    /**
     * Format the result of fixes to the desired format.
     *
     * @param array<string> $metrics
     */
    public function formatFix(InsightCollection $insightCollection, array $metrics): void
    {
        $results = $insightCollection->results();
        $this->style->newLine();

        $totalFix = $results->getTotalFix();
        $totalIssues = $results->getTotalIssues();
        if ($totalFix === 0 && $totalIssues === 0) {
            $this->style->success('Nothing to do. Your code is really clean.');

            return;
        }

        if ($totalFix === 0) {
            $this->style->warning(sprintf(
                'No more issue can be fixed automatically. %s issues remaining',
                $totalIssues
            ));

            return;
        }

        $message = 'issues fixed';
        if ($totalFix === 1) {
            $message = 'issue fixed';
        }

        $this->style->success(sprintf('🧙 ️Congrats ! %s %s', $totalFix, $message));
        $this->style->writeln(sprintf('<fg=yellow;options=bold>%s issues remaining</>', $totalIssues));
        $this->style->newLine();

        foreach ($metrics as $metricClass) {
            $category = explode('\\', $metricClass);
            $category = $category[count($category) - 2];

            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {
                if (! $insight instanceof Fixable || $insight->getTotalFix() === 0) {
                    continue;
                }

                $fix = "<fg=green>• [${category}] </><bold>{$insight->getTitle()}</bold>:";

                $details = $insight->getFixPerFile();
                /** @var Details $detail */
                foreach ($details as $detail) {
                    $detailString = $this->formatFileLine(
                        $detail,
                        $category,
                        $insightCollection->getCollector()->getCommonPath()
                    );

                    if ($detail->hasMessage()) {
                        $detailString .= ($detailString !== '' ? ': ' : '') . $detail->getMessage();
                    }

                    $fix .= PHP_EOL . ' ' . $detailString;
                }

                $this->style->writeln($fix);
                $this->style->newLine();
            }
        }

        $this->style->newLine();
    }

    /**
     * Outputs the summary according to the format.
     *
     * @param array<string> $paths
     */
    private function summary(Results $results, array $paths): self
    {
        $this->style->newLine(2);

        foreach ($paths as $path) {
            $this->style->writeln(
                sprintf(
                    '<fg=yellow>[%s]</> `%s`',
                    date('Y-m-d H:i:s'),
                    $path
                )
            );
        }

        $this->style->newLine();

        $this->renderBlocksScores([
            '%quality%' => self::getPercentageAsString($results->getCodeQuality()),
            '%quality_color%' => "bg={$this->getColor($results->getCodeQuality())}",
            '%complexity%' => self::getPercentageAsString($results->getComplexity()),
            '%complexity_color%' => "bg={$this->getColor($results->getComplexity())}",
            '%structure%' => self::getPercentageAsString($results->getStructure()),
            '%structure_color%' => "bg={$this->getColor($results->getStructure())}",
            '%style%' => self::getPercentageAsString($results->getStyle()),
            '%style_color%' => "bg={$this->getColor($results->getStyle())}",
            '%subtitle%' => self::SUBTITLE,
        ]);

        $this->style->newLine(2);
        $this->style->writeln('Score scale: <fg=red>◼</> 1-49 <fg=yellow>◼</> 50-79 <fg=green>◼</> 80-100');

        return $this;
    }

    /**
     * Outputs the code errors according to the format.
     */
    private function code(InsightCollection $insightCollection, Results $results): self
    {
        $this->style->newLine();
        $this->style->writeln(sprintf(
            '[CODE] %s within <title>%s</title> lines',
            "<fg={$this->getColor($results->getCodeQuality())};options=bold>{$results->getCodeQuality()} pts</>",
            (new Code())->getValue($insightCollection->getCollector())
        ));

        $this->style->newLine();

        $lines = [];
        foreach (self::CODE_METRIC_CLASSES as $metric) {
            $name = explode('\\', $metric);
            $lines[(string) end($name)] = (float) (new $metric())->getPercentage($insightCollection->getCollector());
        }

        $this->writePercentageLines($lines);

        return $this;
    }

    /**
     * Outputs the complexity errors according to the format.
     */
    private function complexity(InsightCollection $insightCollection, Results $results): self
    {
        $this->style->newLine();

        $this->style->writeln(sprintf(
            '[COMPLEXITY] %s with average of <title>%s</title> cyclomatic complexity',
            "<fg={$this->getColor($results->getComplexity())};options=bold>{$results->getComplexity()} pts</>",
            (new Complexity())->getAvg($insightCollection->getCollector())
        ));

        return $this;
    }

    /**
     * Outputs the architecture errors according to the format.
     */
    private function architecture(InsightCollection $insightCollection, Results $results): self
    {
        $this->style->newLine();

        $this->style->writeln(sprintf(
            '[ARCHITECTURE] %s within <title>%s</title> files',
            "<fg={$this->getColor($results->getStructure())};options=bold>{$results->getStructure()} pts</>",
            (new Files())->getValue($insightCollection->getCollector())
        ));

        $this->style->newLine();

        $lines = [];
        foreach (self::ARCHITECTURE_METRIC_CLASSES as $metric) {
            $name = explode('\\', $metric);
            $lines[(string) end($name)] = (float) (new $metric())->getPercentage($insightCollection->getCollector());
        }

        $this->writePercentageLines($lines);

        return $this;
    }

    /**
     * Outputs the miscellaneous errors according to the format.
     */
    private function miscellaneous(Results $results): self
    {
        $this->style->newLine();

        $message = sprintf(
            '[MISC] %s on coding style',
            "<fg={$this->getColor($results->getStyle())};options=bold>{$results->getStyle()} pts</>"
        );

        if ($results->hasInsightInCategory(ForbiddenSecurityIssues::class, 'Security')) {
            $totalSecurityIssuesColor = $results->getTotalSecurityIssues() === 0 ? 'green' : 'red';
            $message .= sprintf(
                ' and %s encountered',
                "<fg={$totalSecurityIssuesColor};options=bold>{$results->getTotalSecurityIssues()} security issues</>"
            );
        }

        $this->style->writeln($message);

        return $this;
    }

    /**
     * Outputs the issues errors according to the format.
     *
     * @param array<string> $metrics
     */
    private function issues(InsightCollection $insightCollection, array $metrics, string $commonPath): self
    {
        $previousCategory = null;
        $detailsComparator = new DetailsComparator();

        foreach ($metrics as $metricClass) {
            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {
                if (! $insight->hasIssue()) {
                    continue;
                }

                $category = explode('\\', $metricClass);
                $category = $category[count($category) - 2];

                if ($previousCategory !== $category) {
                    $this->style->waitForKey($category);
                }

                $previousCategory = $category;

                $issue = "\n<fg=red>•</> [${category}] <bold>{$insight->getTitle()}</bold>";

                if (! $insight instanceof HasDetails && ! $this->style->getOutput()->isVerbose()) {
                    $this->style->writeln($issue);

                    continue;
                }

                $issue .= ':';
                if ($this->style->getOutput()->isVerbose()) {
                    $issue .= " ({$insight->getInsightClass()})";
                }

                if (! $insight instanceof HasDetails) {
                    $this->style->writeln($issue);

                    continue;
                }

                $details = $insight->getDetails();
                usort($details, $detailsComparator);
                $totalDetails = count($details);

                if (! $this->style->getOutput()->isVerbose()) {
                    $details = array_slice($details, -3, 3, true);
                }

                /** @var \NunoMaduro\PhpInsights\Domain\Details $detail */
                foreach ($details as $detail) {
                    $detailString = $this->formatFileLine($detail, $category, $commonPath);

                    if ($detail->hasFunction()) {
                        $detailString .= ($detailString !== '' ? ':' : '') . $detail->getFunction();
                    }

                    if ($detail->hasMessage()) {
                        $detailString .= ($detailString !== '' ? ': ' : '') . $this->parseDetailMessage($detail);
                    }

                    $issue .= "\n  ${detailString}";
                }

                if (! $this->style->getOutput()->isVerbose() && $totalDetails > 3) {
                    $totalRemainDetails = $totalDetails - 3;

                    $issue .= "\n  <fg=red>+{$totalRemainDetails} issues omitted</>";
                }

                $this->style->writeln($issue);
            }
        }

        return $this;
    }

    /**
     * Returns the percentage as 5 chars string.
     */
    private static function getPercentageAsString(float $percentage): string
    {
        $percentageString = sprintf('%s%%', $percentage === 100.0
            ? '100 '
            : number_format($percentage, 1, '.', ''));

        return str_pad($percentageString, 5);
    }

    /**
     * Returns the color for the given percentage.
     */
    private function getColor(float $percentage): string
    {
        if ($percentage >= 80) {
            return 'green';
        }

        if ($percentage >= 50) {
            return 'yellow';
        }

        return 'red';
    }

    /**
     * @param array<string, float|string> $lines
     */
    private function writePercentageLines(array $lines): void
    {
        $dottedLineLength = $this->totalWidth <= 70 ? $this->totalWidth : 70;

        foreach ($lines as $name => $percentage) {
            $percentage = number_format((float) $percentage, 1, '.', '');
            $takenSize = strlen($name . $percentage) + 4; // adding 3 space and percent sign

            $this->style->writeln(sprintf(
                '%s %s %s %%',
                $name,
                str_repeat('.', $dottedLineLength - $takenSize),
                $percentage
            ));
        }
    }

    /**
     * @param array<string, string> $templates
     */
    private function renderBlocksScores(array $templates): void
    {
        $blockSize = self::BLOCK_SIZE;
        $disposition = self::ALL_BLOCKS_IN_ROW; // 4 blocks in a row
        $spaceWidth = $this->getSpaceWidth($this->totalWidth, $blockSize, $disposition);

        if ($this->totalWidth < (($blockSize * $disposition) + 5 * $spaceWidth)) {
            $disposition = self::TWO_BLOCKS_IN_ROW; // Two block in a row
            $spaceWidth = $this->getSpaceWidth($this->totalWidth, $blockSize, $disposition);
        }

        $templates = array_merge($templates, [
            '%block_size%' => str_pad('', $blockSize),
        ]);

        $styleDefinition = clone Table::getStyleDefinition('compact');

        $styleDefinition->setVerticalBorderChars(
            str_pad('', (int) floor($spaceWidth / 2)), // outside
            '' // inside
        );

        $styleDefinition->setPadType(STR_PAD_BOTH);
        $styleDefinition->setCellRowContentFormat('%s');

        $table = new Table($this->style);
        $table->setStyle($styleDefinition);

        $table->setColumnWidth(0, $blockSize + $spaceWidth);
        $table->setColumnWidth(1, $blockSize + $spaceWidth);
        $table->setColumnWidth(2, $blockSize + $spaceWidth);
        $table->setColumnWidth(3, $blockSize + $spaceWidth);

        if ($disposition === self::ALL_BLOCKS_IN_ROW) {
            $table->setRows([
                [
                    strtr(self::QUALITY, $templates),
                    strtr(self::COMPLEXITY, $templates),
                    strtr(self::STRUCTURE, $templates),
                    strtr(self::STYLE, $templates),
                ],
                ['', '', '', ''],
                [
                    strtr('<%subtitle%>Code</>', $templates),
                    strtr('<%subtitle%> Complexity</>', $templates),
                    strtr('<%subtitle%> Architecture</>', $templates),
                    strtr('<%subtitle%>Style</>', $templates),
                ],
            ]);
        }

        if ($disposition === self::TWO_BLOCKS_IN_ROW) {
            $table->setRows([
                [
                    strtr(self::QUALITY, $templates),
                    strtr(self::COMPLEXITY, $templates),
                ],
                ['', ''],
                [
                    strtr('<%subtitle%>Code</>', $templates),
                    strtr('<%subtitle%> Complexity</>', $templates),
                ],
                ['', ''],
                [
                    strtr(self::STRUCTURE, $templates),
                    strtr(self::STYLE, $templates),
                ],
                ['', ''],
                [
                    strtr('<%subtitle%> Architecture</>', $templates),
                    strtr('<%subtitle%>Style</>', $templates),
                ],
            ]);
        }

        $table->render();
    }

    /**
     * Total width of terminal - block size * disposition (4 or 2) / number of space block.
     */
    private function getSpaceWidth(int $totalWidth, int $blockSize, int $disposition): int
    {
        $spaceWidth = (int) floor(($totalWidth - $blockSize * $disposition) / ($disposition + 1));

        if ($spaceWidth > self::MAX_SPACEWIDTH) {
            $spaceWidth = self::MAX_SPACEWIDTH;
        }

        if ($spaceWidth < self::MIN_SPACEWIDTH) {
            $spaceWidth = self::MIN_SPACEWIDTH;
        }

        return $spaceWidth;
    }

    private function getCategoryColor(string $category): string
    {
        return self::CATEGORY_COLOR[$category] ?? 'blue';
    }

    private function formatFileLine(Details $detail, string $category, string $commonPath): string
    {
        $file = $detail->hasFile() ? $detail->getFile() : null;
        $detailString = PathShortener::fileName($detail, $commonPath);

        if ($detail->hasLine()) {
            $detailString .= ($detailString !== '' ? ':' : '') . $detail->getLine();
        }

        $formattedLink = null;
        if ($file !== null) {
            $formattedLink = $this->fileLinkFormatter->format($file, $detail->getLine());
        }

        $color = $this->getCategoryColor($category);
        $detailString = sprintf('<fg=%s>%s</>', $color, $detailString);

        if ($this->supportHyperLinks &&
            $formattedLink !== '' &&
            $detailString !== ''
        ) {
            $detailString = sprintf(
                '<href=%s>%s</>',
                $formattedLink,
                $detailString
            );
        }

        return $detailString;
    }

    private function parseDetailMessage(Details $detail): string
    {
        if ($detail->hasDiff()) {
            $hasColor = false;
            $detailString = '';

            foreach (explode(PHP_EOL, $detail->getMessage()) as $line) {
                if (mb_strpos($line, '-') === 0) {
                    $hasColor = true;
                    $detailString .= '<fg=red>';
                }

                if (mb_strpos($line, '+') === 0) {
                    $hasColor = true;
                    $detailString .= '<fg=green>';
                }

                $detailString .= $line . PHP_EOL;

                if ($hasColor) {
                    $hasColor = false;
                    $detailString .= '</>';
                }
            }

            return $detailString;
        }

        return $detail->getMessage();
    }
}
