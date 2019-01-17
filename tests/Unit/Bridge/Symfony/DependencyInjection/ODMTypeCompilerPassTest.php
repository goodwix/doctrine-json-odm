<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Unit\Bridge\Symfony\DependencyInjection;

use Doctrine\Common\Annotations\Reader;
use Goodwix\DoctrineJsonOdm\Annotation\ODM;
use Goodwix\DoctrineJsonOdm\Bridge\Symfony\DependencyInjection\ODMTypeCompilerPass;
use Goodwix\DoctrineJsonOdm\Tests\Resources\ODM\DummyODM;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ODMTypeCompilerPassTest extends TestCase
{
    private const ODM_PATHS           = 'goodwix.doctrine_json_odm.odm_paths';
    private const ODM_PATHS_VALUE     = [__DIR__.'/../../../../Resources/ODM'];
    private const ODM_AUTO_REGISTRAR  = 'goodwix.doctrine_json_odm.odm_auto_registrar';
    private const ANNOTATION_READER   = 'annotation_reader';

    /** @var ContainerBuilder */
    private $container;

    /** @var Reader */
    private $reader;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerBuilder::class);
        $this->reader    = \Phake::mock(Reader::class);
    }

    /** @test */
    public function process_containerHasValidODMClasses_ODMClassesSetToODMAutoRegistrar(): void
    {
        $pass = new ODMTypeCompilerPass();
        $this->givenContainer_has_returnsTrue();
        $this->givenContainer_hasParameter_returnsTrue();
        $this->givenContainer_getParameter_returnsValue(self::ODM_PATHS, self::ODM_PATHS_VALUE);
        $definition = $this->givenContainer_getDefinition_returnsDefinition();
        $this->givenContainer_get_returnsAnnotationReader(self::ANNOTATION_READER);
        $this->givenReader_getClassAnnotation_returns(new ODM());

        $pass->process($this->container);

        $this->assertContainer_has_wasCalledOnceWithServiceId(self::ODM_AUTO_REGISTRAR);
        $this->assertContainer_hasParameter_wasCalledOnceWithParameterName(self::ODM_PATHS);
        $this->assertContainer_getDefinition_wasCalledOnceWithServiceId(self::ODM_AUTO_REGISTRAR);
        $this->assertContainer_get_wasCalledOnceWithServiceId(self::ANNOTATION_READER);
        $this->assertReader_getClassAnnotation_wasCalledOnceWithReflectionClassAndAnnotationName(DummyODM::class, ODM::class);
        $this->assertDefinition_replaceArgument_wasCalledOnceWithIndexAndValue($definition, 1, [DummyODM::class]);
    }

    /** @test */
    public function process_containerHasNoODMClasses_ODMClassesNotSetToODMAutoRegistrar(): void
    {
        $pass = new ODMTypeCompilerPass();
        $this->givenContainer_has_returnsTrue();
        $this->givenContainer_hasParameter_returnsTrue();
        $this->givenContainer_getParameter_returnsValue(self::ODM_PATHS, self::ODM_PATHS_VALUE);
        $definition = $this->givenContainer_getDefinition_returnsDefinition();
        $this->givenContainer_get_returnsAnnotationReader(self::ANNOTATION_READER);
        $this->givenReader_getClassAnnotation_returns(null);

        $pass->process($this->container);

        $this->assertContainer_has_wasCalledOnceWithServiceId(self::ODM_AUTO_REGISTRAR);
        $this->assertContainer_hasParameter_wasCalledOnceWithParameterName(self::ODM_PATHS);
        $this->assertContainer_getDefinition_wasCalledOnceWithServiceId(self::ODM_AUTO_REGISTRAR);
        $this->assertContainer_get_wasCalledOnceWithServiceId(self::ANNOTATION_READER);
        $this->assertReader_getClassAnnotation_wasCalledOnceWithReflectionClassAndAnnotationName(DummyODM::class, ODM::class);
        $this->assertDefinition_replaceArgument_wasCalledOnceWithIndexAndValue($definition, 1, []);
    }

    private function assertContainer_get_wasCalledOnceWithServiceId($serviceId): void
    {
        \Phake::verify($this->container)
            ->get($serviceId);
    }

    private function givenContainer_get_returnsAnnotationReader($serviceId): void
    {
        \Phake::when($this->container)
            ->get($serviceId)
            ->thenReturn($this->reader);
    }

    private function givenContainer_getParameter_returnsValue(string $name, $value): void
    {
        \Phake::when($this->container)
            ->getParameter($name)
            ->thenReturn($value);
    }

    private function givenContainer_getDefinition_returnsDefinition(): Definition
    {
        $definition = \Phake::mock(Definition::class);

        \Phake::when($this->container)
            ->getDefinition(\Phake::anyParameters())
            ->thenReturn($definition);

        return $definition;
    }

    private function assertContainer_getDefinition_wasCalledOnceWithServiceId(string $serviceId): void
    {
        \Phake::verify($this->container)
            ->getDefinition($serviceId);
    }

    private function givenContainer_has_returnsTrue(): void
    {
        \Phake::when($this->container)
            ->has(\Phake::anyParameters())
            ->thenReturn(true);
    }

    private function givenContainer_hasParameter_returnsTrue(): void
    {
        \Phake::when($this->container)
            ->hasParameter(\Phake::anyParameters())
            ->thenReturn(true);
    }

    private function assertContainer_hasParameter_wasCalledOnceWithParameterName(string $parameterName): void
    {
        \Phake::verify($this->container)
            ->hasParameter($parameterName);
    }

    private function assertContainer_has_wasCalledOnceWithServiceId(string $serviceId): void
    {
        \Phake::verify($this->container)
            ->has($serviceId);
    }

    private function assertDefinition_replaceArgument_wasCalledOnceWithIndexAndValue(
        Definition $definition,
        $index,
        $value
    ): void {
        \Phake::verify($definition)
            ->replaceArgument($index, $value);
    }

    private function assertReader_getClassAnnotation_wasCalledOnceWithReflectionClassAndAnnotationName(
        string $reflectionClass,
        string $annotationClass
    ): void {
        /** @var \ReflectionClass $class */
        \Phake::verify($this->reader)
            ->getClassAnnotation(\Phake::capture($class), $annotationClass);
        $this->assertSame($reflectionClass, $class->getName());
    }

    private function givenReader_getClassAnnotation_returns(?ODM $ODM): void
    {
        \Phake::when($this->reader)
            ->getClassAnnotation(\Phake::anyParameters())
            ->thenReturn($ODM);
    }
}
