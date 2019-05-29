<?php

declare(strict_types=1);

namespace Tests\Application;

use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenPrivateMethods;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PHPUnit\Framework\TestCase;

final class LaravelConfigExtendingTest extends TestCase
{
    /**
     * @var array
     */
    private $laravelPresetConfig;

    /**
     * @var array
     */
    private $configExtending;

    public function setUp(): void
    {
        parent::setUp();
        $this->laravelPresetConfig = Preset::get();
        $this->configExtending = require __DIR__ . '/../Fixtures/ConfigFiles/Laravel/config-extending.php';
    }

    public function testExtendingConfigKey_Exclude(): void
    {
        $excludeCountOnPreset = count($this->laravelPresetConfig['exclude']);
        $excludeCountOnExtending = count($this->configExtending['exclude']);
        $finalConfig = ConfigResolver::resolve($this->configExtending, '');

        $this->assertEquals(11, $excludeCountOnPreset);
        $this->assertEquals(3, $excludeCountOnExtending);
        $this->assertCount($excludeCountOnPreset + $excludeCountOnExtending, $finalConfig['exclude']);
    }

    public function testExtendingConfigKey_Add(): void
    {
        $addCountOnPreset = count($this->laravelPresetConfig['add']);
        $addCountOnExtending = count($this->configExtending['add']);
        $finalConfig = ConfigResolver::resolve($this->configExtending, '');

        $this->assertEquals(0, $addCountOnPreset);
        $this->assertEquals(1, $addCountOnExtending);
        $this->assertCount($addCountOnPreset + $addCountOnExtending, $finalConfig['add']);
    }

    public function testExtendingConfigKey_Remove(): void
    {
        $removeCountOnPreset = count($this->laravelPresetConfig['remove']);
        $removeCountOnExtending = count($this->configExtending['remove']);
        $finalConfig = ConfigResolver::resolve($this->configExtending, '');

        $this->assertEquals(0, $removeCountOnPreset);
        $this->assertEquals(7, $removeCountOnExtending);
        $this->assertCount($removeCountOnPreset + $removeCountOnExtending, $finalConfig['remove']);
    }

    public function testExtendingConfigKey_Config(): void
    {
        $configCountOnPreset = count($this->laravelPresetConfig['config']);
        $configCountOnExtending = count($this->configExtending['config']);
        $finalConfig = ConfigResolver::resolve($this->configExtending, '');

        $this->assertEquals(2, $configCountOnPreset);
        $this->assertEquals(1, $configCountOnExtending);
        $this->assertCount($configCountOnPreset + $configCountOnExtending, $finalConfig['config']);

        $this->assertArrayHasKey(ForbiddenDefineGlobalConstants::class, $finalConfig['config']);
        $this->assertArrayHasKey(ForbiddenFunctionsSniff::class, $finalConfig['config']);
        $this->assertArrayHasKey(ForbiddenPrivateMethods::class, $finalConfig['config']);
    }
}
