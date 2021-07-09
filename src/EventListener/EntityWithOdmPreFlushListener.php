<?php
declare(strict_types=1);

namespace Goodwix\DoctrineJsonOdm\EventListener;

use Goodwix\DoctrineJsonOdm\EntityWithOdmInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EntityWithOdmPreFlushListener
{
    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $identityMap = $args->getEntityManager()->getUnitOfWork()->getIdentityMap();
        foreach ($identityMap as $map) {
            foreach ($map as $entity) {
                if (null === $entity) {
                    continue;
                }
                if (is_a($entity, EntityWithOdmInterface::class)) {
                    $this->handle($entity);
                }
            }
        }
    }

    private function handle(EntityWithOdmInterface $entity): void
    {
        $fieldList = $entity->getOdmFieldList();
        foreach ($fieldList as $fieldName) {
            if (!$this->propertyAccessor->isReadable($entity, $fieldName)) {
                continue;
            }

            if (!$this->propertyAccessor->isWritable($entity, $fieldName)) {
                continue;
            }

            $object = $this->propertyAccessor->getValue($entity, $fieldName);
            if (null === $object) {
                continue;
            }

            $clonedObject = clone $object;
            $this->propertyAccessor->setValue($entity, $fieldName, $clonedObject);
        }
    }
}
