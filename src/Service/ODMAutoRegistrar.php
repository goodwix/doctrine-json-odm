<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Service;

use Doctrine\Common\Annotations\Reader;
use Goodwix\DoctrineJsonOdm\Annotation\ODM;
use Goodwix\DoctrineJsonOdm\Type\ODMArrayType;
use Goodwix\DoctrineJsonOdm\Type\ODMType;
use Symfony\Component\Serializer\SerializerInterface;

class ODMAutoRegistrar
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var Reader */
    private $annotationReader;

    /** @var string[] */
    private $entityClassList;

    public function __construct(SerializerInterface $serializer, Reader $annotationReader, array $entityClassList)
    {
        $this->serializer       = $serializer;
        $this->annotationReader = $annotationReader;
        $this->entityClassList  = $entityClassList;
    }

    public function registerODMTypes(): void
    {
        foreach ($this->entityClassList as $entityClass) {
            $this->assertClassExists($entityClass);

            if (!ODMType::hasType($entityClass)) {
                $annotation = $this->readODMAnnotation($entityClass);
                $this->registerODMType($entityClass, $annotation);
            }
        }
    }

    private function assertClassExists(string $entityClass): void
    {
        if (!class_exists($entityClass) && !interface_exists($entityClass)) {
            throw new \DomainException(
                sprintf('Invalid ODM entity class: "%s".', $entityClass)
            );
        }
    }

    private function readODMAnnotation(string $entityClass): ODM
    {
        $reflectionClass = new \ReflectionClass($entityClass);
        $annotation      = $this->annotationReader->getClassAnnotation($reflectionClass, ODM::class);

        if (!$annotation instanceof ODM) {
            throw new \DomainException(
                sprintf('ODM class "%s" has no valid annotation.', $entityClass)
            );
        }

        return $annotation;
    }

    private function registerODMType(string $entityClass, ODM $annotation): void
    {
        ODMType::registerODMType($entityClass, $this->serializer);
        ODMArrayType::registerODMType($entityClass, $this->serializer);

        /** @var ODMType $type */
        $type = ODMType::getType($entityClass);
        $type->setSerializationContext($annotation->serializationContext);
        $type->setDeserializationContext($annotation->deserializationContext);

        /** @var ODMArrayType $type */
        $type = ODMArrayType::getType(sprintf('%s[]', $entityClass));
        $type->setSerializationContext($annotation->serializationContext);
        $type->setDeserializationContext($annotation->deserializationContext);
    }
}
