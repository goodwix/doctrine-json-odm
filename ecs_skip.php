<?php

use PhpCsFixer\Fixer\FunctionNotation\SingleLineThrowFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;

return [
    PhpUnitMethodCasingFixer::class                                                                              => null,
    NotOperatorWithSuccessorSpaceFixer::class                                                                    => null,
    'PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff.CloseBracketLine'        => null,
    'PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ControlStructureSpacingSniff.SpacingAfterOpenBrace' => null,
    NoSuperfluousPhpdocTagsFixer::class                                                                          => null,
    DeclareStrictTypesFixer::class                                                                               => null,
    'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff'                                           => null,
    PhpdocLineSpanFixer::class                                                                                   => null,
    ArrayOpenerAndCloserNewlineFixer::class                                                                      => null,
    ArrayListItemNewlineFixer::class                                                                             => null,
    SingleLineThrowFixer::class                                                                                  => null,
    StandaloneLineInMultilineArrayFixer::class                                                                   => null,
];
