<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Bridge\Symfony\DependencyInjection;

use Doctrine\Common\Annotations\Reader;
use Goodwix\DoctrineJsonOdm\Annotation\ODM;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ODMTypeCompilerPass implements CompilerPassInterface
{
    public const ODM_AUTO_REGISTRAR = 'goodwix.doctrine_json_odm.odm_auto_registrar';
    public const ODM_PATHS          = 'goodwix.doctrine_json_odm.odm_paths';

    /** @var Reader */
    private $reader;

    public function process(ContainerBuilder $container): void
    {
        if ($container->has(self::ODM_AUTO_REGISTRAR) && $container->hasParameter(self::ODM_PATHS)) {
            $this->reader = $container->get('annotation_reader');

            $paths           = $container->getParameter(self::ODM_PATHS);
            $entityClassList = $this->collectEntityClassListFromPaths($paths);

            $definition = $container->getDefinition(self::ODM_AUTO_REGISTRAR);
            $definition->replaceArgument(1, $entityClassList);
        }
    }

    private function collectEntityClassListFromPaths(array $paths): array
    {
        $classes = $this->getReflectionClassesFromDirectories($paths);

        $entityClassList = [];

        /** @var \ReflectionClass $class */
        foreach ($classes as $class) {
            $odmAnnotation = $this->reader->getClassAnnotation($class, ODM::class);

            if (null !== $odmAnnotation) {
                $entityClassList[] = $class->getName();
            }
        }

        return $entityClassList;
    }

    private function getReflectionClassesFromDirectories(array $directories): \Iterator
    {
        foreach ($directories as $path) {
            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+\.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (!preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                require_once $sourceFile;

                $includedFiles[$sourceFile] = true;
            }
        }

        $declared = get_declared_classes();
        foreach ($declared as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile      = $reflectionClass->getFileName();
            if (isset($includedFiles[$sourceFile])) {
                yield $className => $reflectionClass;
            }
        }
    }
}
