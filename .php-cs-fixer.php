<?php

$config = new PhpCsFixer\Config();
return $config
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_empty_statement' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'protected_to_private' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in(__DIR__ . '/src/')
    )
;
