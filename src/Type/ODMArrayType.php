<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Type;

use Symfony\Component\Serializer\SerializerInterface;

class ODMArrayType extends AbstractODMType
{
    public static function registerODMType(string $entityClass, SerializerInterface $serializer): void
    {
        if (!class_exists($entityClass) && !interface_exists($entityClass)) {
            throw new \DomainException(sprintf('Class or interface "%s" does not exist.', $entityClass));
        }

        $typeName = sprintf('%s[]', $entityClass);
        self::addType($typeName, static::class);

        /** @var ODMType $type */
        $type = self::getType($typeName);
        $type->setEntityClass($typeName);
        $type->setSerializer($serializer);
    }
}