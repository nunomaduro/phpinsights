<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use Exception;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/**
 * @internal
 */
final class Html implements Formatter
{
    /** @var OutputInterface */
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Format the result to the desired format.
     *
     * @param InsightCollection $insightCollection
     * @param string $dir
     * @param array<string> $metrics
     *
     * @throws Exception
     */
    public function format(
        InsightCollection $insightCollection,
        string $dir,
        array $metrics
    ): void
    {
        $this->output->write($this->getTwig()->render('dashboard.html.twig', [
            'dir' => $dir,
            'results' => $insightCollection->results(),
            'insights' => $insightCollection,
        ]), false, OutputInterface::OUTPUT_RAW);
    }

    private function getTwig(): Twig
    {
        $loader = new FilesystemLoader(__DIR__.'/../../../../views');
        $twig = new Twig($loader, [
            'cache' => false,
            'debug' => true,
        ]);

        $twig->addFilter(new TwigFilter('sluggify', static function (string $slug): string {
            $slug = preg_replace('/<(.*?)>/u', '', (string) $slug);
            $slug = preg_replace('/[\'"‘’“”]/u', '', (string) $slug);
            $slug = mb_strtolower((string) $slug, 'UTF-8');

            preg_match_all('/[\p{L}\p{N}\.]+/u', (string) $slug, $words);

            return implode('-', array_filter($words[0]));
        }));

        return $twig;
    }
}
