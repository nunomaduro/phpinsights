<?php

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/bin']);
    $parameters->set(Option::AUTOLOAD_PATHS, [
        __DIR__ . '/vendor/squizlabs/php_codesniffer/autoload.php',
    ]);

    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    // skip root namespace classes, like \DateTime or \Exception [default: true]
    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);
    // skip classes used in PHP DocBlocks, like in /** @var \Some\Class */ [default: true]
    $parameters->set(Option::IMPORT_DOC_BLOCKS, false);
    // Run Rector only on changed files
    $parameters->set(Option::ENABLE_CACHE, true);

    // Path to phpstan with extensions, that PHPSTan in Rector uses to determine types
    //$parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, getcwd() . '/phpstan.neon.dist');

    $parameters->set(Option::SETS, [
        SetList::ARRAY_STR_FUNCTIONS_TO_STATIC_CALL,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::DOCTRINE_CODE_QUALITY,
        SetList::EARLY_RETURN,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PHP_73,
        SetList::PHP_74,
        SetList::PHPUNIT_CODE_QUALITY,
    ]);
};
