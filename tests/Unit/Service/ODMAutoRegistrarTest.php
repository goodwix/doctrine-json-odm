<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Unit\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Goodwix\DoctrineJsonOdm\Annotation\ODM;
use Goodwix\DoctrineJsonOdm\Service\ODMAutoRegistrar;
use Goodwix\DoctrineJsonOdm\Tests\Resources\ODM\DummyODM;
use Goodwix\DoctrineJsonOdm\Type\ODMType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ODMAutoRegistrarTest extends TestCase
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var Reader */
    private $annotationReader;

    protected function setUp(): void
    {
        $this->serializer       = \Phake::mock(SerializerInterface::class);
        $this->annotationReader = \Phake::mock(AnnotationReader::class);
    }

    /** @test */
    public function registerODMTypes_entityClass_odmTypeRegistered(): void
    {
        $registrar = $this->createODMAutoRegistrar([self::class]);
        $odm       = $this->givenODMAnnotation();
        $this->givenAnnotationForOdmClass($odm);

        $registrar->registerODMTypes();

        $this->assertAnnotationWasReadFromOdmClass();
        $this->assertODMTypeWasRegisteredWithExpectedAttributes($odm);
    }

    /** @test */
    public function registerODMTypes_classWithoutAnnotation_domainException(): void
    {
        $registrar = $this->createODMAutoRegistrar([DummyODM::class]);
        $this->givenNoAnnotationForOdmClass();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('ODM class "Goodwix\DoctrineJsonOdm\Tests\Resources\ODM\DummyODM" has no valid annotation.');

        $registrar->registerODMTypes();
    }

    /** @test */
    public function registerODMTypes_notAClass_domainException(): void
    {
        $registrar = $this->createODMAutoRegistrar(['notAClass']);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invalid ODM entity class: "notAClass".');

        $registrar->registerODMTypes();
    }

    private function createODMAutoRegistrar(array $classes): ODMAutoRegistrar
    {
        return new ODMAutoRegistrar($this->serializer, $this->annotationReader, $classes);
    }

    private function assertAnnotationWasReadFromOdmClass(): void
    {
        \Phake::verify($this->annotationReader)
            ->getClassAnnotation(\Phake::capture($class), ODM::class);
        $this->assertInstanceOf(\ReflectionClass::class, $class);
    }

    private function givenNoAnnotationForOdmClass(): void
    {
        \Phake::when($this->annotationReader)
            ->getClassAnnotation(\Phake::anyParameters())
            ->thenReturn(null);
    }

    private function givenAnnotationForOdmClass(ODM $odm): void
    {
        \Phake::when($this->annotationReader)
            ->getClassAnnotation(\Phake::anyParameters())
            ->thenReturn($odm);
    }

    private function givenODMAnnotation(): ODM
    {
        $odm                         = new ODM();
        $odm->serializationContext   = ['serializationContext'];
        $odm->deserializationContext = ['deserializationContext'];

        return $odm;
    }

    private function assertODMTypeWasRegisteredWithExpectedAttributes(ODM $odm): void
    {
        $this->assertTrue(ODMType::hasType(self::class));
        /** @var ODMType $type */
        $type = ODMType::getType(self::class);
        $this->assertSame($odm->serializationContext, $type->getSerializationContext());
        $this->assertSame($odm->deserializationContext, $type->getDeserializationContext());
    }
}
