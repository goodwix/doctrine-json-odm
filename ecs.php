<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PhpUnitTestAnnotationFixer::class)
        ->call('configure', [['style' => 'annotation']]);

    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [['spacing' => 'none']]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SETS, [
        SetList::CLEAN_CODE,
        SetList::COMMON,
        SetList::SYMFONY,
        SetList::SYMFONY_RISKY,
        SetList::PSR_12,
    ]);

    $skip = include 'ecs_skip.php';
    $parameters->set(Option::SKIP, $skip);
};
