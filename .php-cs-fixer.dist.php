<?php

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$header = <<<EOF
This file is part of the ekino/sonata project.

(c) Ekino

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->in(['src','tests'])
;

$config = new PhpCsFixer\Config();

$config->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setFinder($finder);

return $config->setRules([
    '@Symfony' => true,
    'array_syntax' => [
        'syntax' => 'short'
    ],
    'binary_operator_spaces' => ['default' => 'align', 'operators' => ['=>' => 'align', '=' => 'align_single_space']],
    'class_attributes_separation' => true,
    'header_comment' => [
        'header' => $header
    ],
    'linebreak_after_opening_tag' => true,
    'native_function_invocation' => ['include' => ['@compiler_optimized']],
    'no_extra_blank_lines' => [
        'tokens' => [
            'break',
            'continue',
            'extra',
            'return',
            'throw',
            'use',
            'parenthesis_brace_block',
            'square_brace_block',
            'curly_brace_block'
        ]
    ],
    'echo_tag_syntax' => ['format' => 'long'],
    'no_useless_else'   => true,
    'no_useless_return' => true,
    'ordered_imports'   => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_order' => true,
])
;
