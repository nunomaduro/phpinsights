<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use Exception;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigTest;

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
        $loader = new FilesystemLoader(__DIR__.'/../../../../views');
        $twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
        $twig->addExtension(new DebugExtension());
        $twig->addTest(new TwigTest('instanceof', function($var, $instance) {
            $reflexionClass = new \ReflectionClass($instance);
            return $reflexionClass->isInstance($var);
        }));
        $twig->addFilter(new TwigFilter('sluggify', function ($slug) {
            $slug = preg_replace('/<(.*?)>/u', '', $slug);
            $slug = preg_replace('/[\'"‘’“”]/u', '', $slug);
            $slug = mb_strtolower($slug, 'UTF-8');
            preg_match_all('/[\p{L}\p{N}\.]+/u', $slug, $words);
            return implode('-', array_filter($words[0]));
        }));

        $this->output->write($twig->render('dashboard.html.twig', [
            'dir' => $dir,
            'results' => $insightCollection->results(),
            'insights' => $insightCollection,
        ]), false, OutputInterface::OUTPUT_RAW);
    }
}
