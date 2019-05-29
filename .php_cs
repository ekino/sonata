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

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'binary_operator_spaces' => [
            'align_double_arrow' => true,
            'align_equals'       => true
        ],
        'class_attributes_separation' => true,
        'header_comment' => [
            'header' => $header
        ],
        'linebreak_after_opening_tag' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block']
        ],
        'no_short_echo_tag' => true,
        'no_useless_else'   => true,
        'no_useless_return' => true,
        'ordered_imports'   => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
    ])
    ->setUsingCache(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(['src', 'tests'])
    )
;
