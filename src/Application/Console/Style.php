<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use ReflectionClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class Style extends SymfonyStyle
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * Creates a new instance of Style.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($this->input = $input, $this->output = $output);
    }

    /**
     * @param  string  $type
     * @param  string  $letter
     *
     * @return string
     */
    public function letter(string $type, string $letter): string
    {
        $style = new SymfonyStyle($this->input, $output = new BufferedOutput());

        $class = new ReflectionClass(SymfonyStyle::class);
        $property = $class->getProperty('lineLength');
        $property->setAccessible(true);

        $property->setValue($style, 5);

        $style->block($letter, null, 'fg=white;options=bold;bg=green', '  ', true);

        return <<<EOD
<fg=white;options=bold;bg=$type>       </>
<fg=white;options=bold;bg=$type>   $letter   </>
<fg=white;options=bold;bg=$type>       </>
</>
EOD;
    }
}