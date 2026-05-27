<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src/Providers/Nacional',
        __DIR__ . '/tests/Providers/Nacional',
    ])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
    ])
    ->setFinder($finder);
