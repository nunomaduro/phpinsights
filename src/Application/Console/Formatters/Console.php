<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\FileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\DetailsComparator;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\NullFileLinkFormatter;
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
 */
final class Console implements Formatter
{
    private const BLOCK_SIZE = 9;
    private const ALL_BLOCKS_IN_ROW = 4;
    private const TWO_BLOCKS_IN_ROW = 2;
    private const MIN_SPACEWIDTH = 5;
    private const MAX_SPACEWIDTH = 15;

    /**
     * @var Style
     */
    private $style;
    /**
     * @var int
     */
    private $totalWidth;
    /**
     * @var FileLinkFormatter
     */
    private $fileLinkFormatter;
    /**
     * @var bool
     */
    private $supportHyperLinks;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->style = new Style($input, $output);
        $this->totalWidth = (new Terminal())->getWidth();

        $outputFormatterStyle = new OutputFormatterStyle();
        /** @var Configuration $config */
        $config = Container::make()->get(Configuration::class);

        $this->fileLinkFormatter = $config->getFileLinkFormatter();
        $this->supportHyperLinks = method_exists($outputFormatterStyle, 'setHref');
    }

    /**
     * Format the result to the desired format.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insightCollection
     * @param array<string> $directories
     * @param array<int, string> $metrics
     */
    public function format(
        InsightCollection $insightCollection,
        array $directories,
        array $metrics
    ): void {
        $results = $insightCollection->results();

        $this->summary($results, $directories)
            ->code($insightCollection, $results)
            ->complexity($insightCollection, $results)
            ->architecture($insightCollection, $results)
            ->miscellaneous($results);

        $this->issues($insightCollection, $metrics, $directories);
    }

    /**
     * Outputs the summary according to the format.
     *
     * @param Results $results
     * @param array<string> $directories
     *
     * @return self
     */
    private function summary(Results $results, array $directories): self
    {
        $this->style->newLine(2);

        foreach ($directories as $path) {
            $this->style->writeln(
                sprintf(
                    '<fg=yellow>[%s]</> `%s`',
                    date('Y-m-d H:i:s'),
                    $path
                )
            );
        }

        $subtitle = 'fg=white;options=bold;fg=white';
        $this->style->newLine();

        $codeQualityColor = "bg={$this->getColor($results->getCodeQuality())}";
        $complexityColor = "bg={$this->getColor($results->getComplexity())}";
        $structureColor = "bg={$this->getColor($results->getStructure())}";
        $styleColor = "bg={$this->getColor($results->getStyle())}";

        $codeQuality = self::getPercentageAsString($results->getCodeQuality());
        $complexity = self::getPercentageAsString($results->getComplexity());
        $structure = self::getPercentageAsString($results->getStructure());
        $style = self::getPercentageAsString($results->getStyle());

        $this->renderBlocksScores([
            '%quality%' => $codeQuality,
            '%quality_color%' => $codeQualityColor,
            '%complexity%' => $complexity,
            '%complexity_color%' => $complexityColor,
            '%structure%' => $structure,
            '%structure_color%' => $structureColor,
            '%style%' => $style,
            '%style_color%' => $styleColor,
            '%subtitle%' => $subtitle,
        ]);

        $this->style->newLine(2);
        $this->style->writeln('Score scale: <fg=red>◼</> 1-49 <fg=yellow>◼</> 50-79 <fg=green>◼</> 80-100');

        return $this;
    }

    /**
     * Outputs the code errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param Results $results
     *
     * @return self
     */
    private function code(
        InsightCollection $insightCollection,
        Results $results
    ): self {
        $this->style->newLine();
        $this->style->writeln(sprintf('[CODE] %s within <title>%s</title> lines',
            "<fg={$this->getColor($results->getCodeQuality())};options=bold>{$results->getCodeQuality()} pts</>",
            (new Code())->getValue($insightCollection->getCollector())
        ));
        $this->style->newLine();

        $lines = [];
        foreach ([
            Comments::class,
            Classes::class,
            Functions::class,
            Globally::class,
        ] as $metric) {
            $name = explode('\\', $metric);
            $lines[(string) end($name)] = (float) (new $metric())->getPercentage($insightCollection->getCollector());
        }

        $this->writePercentageLines($lines);

        return $this;
    }

    /**
     * Outputs the complexity errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param Results $results
     *
     * @return self
     */
    private function complexity(
        InsightCollection $insightCollection,
        Results $results
    ): self {
        $this->style->newLine();

        $this->style->writeln(sprintf('[COMPLEXITY] %s with average of <title>%s</title> cyclomatic complexity',
            "<fg={$this->getColor($results->getComplexity())};options=bold>{$results->getComplexity()} pts</>",
            (new Complexity())->getAvg($insightCollection->getCollector())
        ));

        return $this;
    }

    /**
     * Outputs the architecture errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param Results $results
     *
     * @return self
     */
    private function architecture(
        InsightCollection $insightCollection,
        Results $results
    ): self {
        $this->style->newLine();

        $this->style->writeln(sprintf('[ARCHITECTURE] %s within <title>%s</title> files',
            "<fg={$this->getColor($results->getStructure())};options=bold>{$results->getStructure()} pts</>",
            (new Files())->getValue($insightCollection->getCollector())
        ));

        $this->style->newLine();

        $lines = [];
        foreach ([
            ArchitectureClasses::class,
            ArchitectureInterfaces::class,
            ArchitectureGlobally::class,
            ArchitectureTraits::class,
        ] as $metric) {
            $name = explode('\\', $metric);
            $lines[(string) end($name)] = (float) (new $metric())->getPercentage($insightCollection->getCollector());
        }

        $this->writePercentageLines($lines);

        return $this;
    }

    /**
     * Outputs the miscellaneous errors according to the format.
     *
     * @param Results $results
     *
     * @return self
     */
    private function miscellaneous(
        Results $results
    ): self {
        $this->style->newLine();

        $message = sprintf(
            '[MISC] %s on coding style',
            "<fg={$this->getColor($results->getStyle())};options=bold>{$results->getStyle()} pts</>");

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
     * @param InsightCollection $insightCollection
     * @param array<string> $metrics
     * @param array<string> $directories
     *
     * @return self
     */
    private function issues(
        InsightCollection $insightCollection,
        array $metrics,
        array $directories
    ): self {
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
                    $detailString = $this->formatFileLine($insightCollection, $detail, $category);

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
     *
     * @param float $percentage
     *
     * @return string
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
     *
     * @param float $percentage
     *
     * @return string
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

            $this->style->writeln(sprintf('%s %s %s %%',
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

        $quality = <<<EOD
<%quality_color%>%block_size%</>
<fg=black;options=bold;%quality_color%>  %quality%  </>
<%quality_color%>%block_size%</>
EOD;
        $complexity = <<<EOD
<%complexity_color%>%block_size%</>
<fg=black;options=bold;%complexity_color%>  %complexity%  </>
<%complexity_color%>%block_size%</>
EOD;
        $structure = <<<EOD
<%structure_color%>%block_size%</>
<fg=black;options=bold;%structure_color%>  %structure%  </>
<%structure_color%>%block_size%</>
EOD;
        $style = <<<EOD
<%style_color%>%block_size%</>
<fg=black;options=bold;%style_color%>  %style%  </>
<%style_color%>%block_size%</>
EOD;

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
                    strtr($quality, $templates),
                    strtr($complexity, $templates),
                    strtr($structure, $templates),
                    strtr($style, $templates),
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
                    strtr($quality, $templates),
                    strtr($complexity, $templates),
                ],
                ['', ''],
                [
                    strtr('<%subtitle%>Code</>', $templates),
                    strtr('<%subtitle%> Complexity</>', $templates),
                ],
                ['', ''],
                [
                    strtr($structure, $templates),
                    strtr($style, $templates),
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
     *
     * @param int $totalWidth
     * @param int $blockSize
     * @param int $disposition
     *
     * @return int
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

    private function getFileLinkFormatter(): FileLinkFormatter
    {
        if ($this->fileLinkFormatter === null) {
            $this->fileLinkFormatter = new NullFileLinkFormatter();
        }

        return $this->fileLinkFormatter;
    }

    private function getCategoryColor(string $category): string
    {
        //black, red, green, yellow, blue, magenta, cyan, white, default
        $categoryColor = [
            'Code' => 'cyan',
            'Complexity' => 'green',
            'Architecture' => 'blue',
            'Style' => 'yellow',
            'Security' => 'red',
        ];
        return $categoryColor[$category] ?? 'blue';
    }

    private function formatFileLine(InsightCollection $insightCollection, Details $detail, string $category): string
    {
        $file = $detail->hasFile() ? $detail->getFile() : null;
        $detailString = PathShortener::fileName($detail, $insightCollection->getCollector()->getCommonPath());

        if ($detail->hasLine()) {
            $detailString .= ($detailString !== '' ? ':' : '') . $detail->getLine();
        }

        $formattedLink = null;
        if ($file !== null) {
            $formattedLink = $this->getFileLinkFormatter()->format($file, $detail->getLine());
        }

        $color = $this->getCategoryColor($category);
        $detailString = sprintf('<fg=%s>%s</>', $color, $detailString);

        if (
            $this->supportHyperLinks &&
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
