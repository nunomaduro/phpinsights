<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\DetailsComparator;
use Tests\TestCase;

final class DetailsComparatorTest extends TestCase
{
    /** @var \NunoMaduro\PhpInsights\Domain\DetailsComparator */
    private $comparator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comparator = new DetailsComparator();
    }

    public function testComparisonWithEmptyDetails(): void
    {
        self::assertEquals(0, ($this->comparator)(new Details(), new Details()));
    }

    public function testComparisonWithDifferentFileNames(): void
    {
        $first = (new Details())->setFile('src/First.php');
        $second = (new Details())->setFile('src/Second.php');

        self::assertEquals(-1, ($this->comparator)($first, $second));
        self::assertEquals(1, ($this->comparator)($second, $first));
    }

    public function testComparisonWithSameFileName(): void
    {
        $first = (new Details())
            ->setFile('src/Foo.php')
            ->setLine(10);

        $second = (new Details())
            ->setFile('src/Foo.php')
            ->setLine(20);

        self::assertEquals(-1, ($this->comparator)($first, $second));
        self::assertEquals(1, ($this->comparator)($second, $first));
    }
}
