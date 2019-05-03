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
        'binary_operator_spaces' => ['align_double_arrow' => true, 'align_equals' => true],
        'array_syntax' => array('syntax' => 'short'),
        'blank_line_after_namespace' => true,
        'header_comment' => array('header' => $header),
        'lowercase_constants' => true,
        'lowercase_keywords' => true,
        'method_separation' => true,
        'no_closing_tag' => true,
        'no_extra_consecutive_blank_lines' => [
            'tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block']
        ],
        'no_short_echo_tag' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'semicolon_after_instruction' => true,
        'visibility_required' => true,
    ])
    ->setUsingCache(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(['src', 'tests'])
    )
;
