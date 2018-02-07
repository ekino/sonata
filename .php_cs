<?php
$header = <<<'HEADER'

This file is part of the Sonata for Ekino project.

(c) 2018 - Ekino

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.

HEADER;
$rules = [
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'header_comment' => [
        'header' => $header,
    ],
    'no_extra_consecutive_blank_lines' => true,
    'no_php4_constructor' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_class_elements' => true,
    'ordered_imports' => true,
    'phpdoc_order' => true,
    '@PHPUnit57Migration:risky' => true,
];
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('Tests/Fixtures')
    ->exclude('tests/Fixtures')
    ->exclude('Resources/skeleton')
    ->exclude('Resources/public/vendor')
;
return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setUsingCache(true)
;
