<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCSIgnored(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        // PSR-12 as the base
        '@PSR12' => true,
        '@PSR12:risky' => true,

        // Strict types
        'declare_strict_types' => true,
        'strict_param' => true,
        'strict_comparison' => true,

        // Import organization
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
        'no_unused_imports' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_functions' => true, 'import_constants' => true],

        // Type hints
        'fully_qualified_strict_types' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'void_return' => true,

        // Code cleanliness
        'no_empty_phpdoc' => true,
        'no_extra_blank_lines' => ['tokens' => ['curly_brace_block', 'extra', 'parenthesis_brace_block', 'square_brace_block']],
        'no_trailing_whitespace' => true,
        'no_trailing_whitespace_in_comment' => true,
        'no_whitespace_in_blank_line' => true,
        'single_blank_line_at_eof' => true,

        // Array formatting
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'parameters', 'arguments']],
        'trim_array_spaces' => true,

        // Modern PHP
        'modernize_strpos' => true,
        'no_alias_functions' => true,

        // PHPDoc
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true, 'allow_mixed' => true],
    ])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
