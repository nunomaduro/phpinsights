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

    public function testSortWithComparator(): void
    {
        $a = $this->makeDetails('app/App/Admin/Filters/ByRuleTemplate.php', 25, 'Unused parameter $request.');
        $b = $this->makeDetails('app/App/Dashboard/Controllers/UserController.php', 54, 'Unused parameter $user');
        $c = $this->makeDetails('app/Support/Rules/HexColor.php', 9, 'Unused parameter $attribute.');
        $d = $this->makeDetails('app/Support/Rules/DomainName.php', 27, 'Unused parameter $values.');
        $e = $this->makeDetails('app/Support/Rules/DomainName.php', 17, 'Unused parameter $attribute.');
        $f = $this->makeDetails('app/Domain/User/Policies/UserPolicy.php', 14, 'Unused parameter $admin.');

        $array = [$a, $b, $c, $d, $e, $f];
        usort($array, $this->comparator);

        self::assertEquals([$a, $b, $f, $e, $d, $c], $array);
    }

    private function makeDetails(string $file, int $line, string $message): Details
    {
        return (new Details())
            ->setFile($file)
            ->setLine($line)
            ->setMessage($message);
    }
}
