<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Symfony\Component\Serializer\SerializerInterface;

class ODMType extends JsonType
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var string */
    private $format = 'json';

    /** @var string */
    private $entityClass;

    public function getSerializer(): SerializerInterface
    {
        if (null === $this->serializer) {
            throw new \RuntimeException(
                sprintf(
                    'An instance of "%s" must be available. Call the "setSerializer" method.',
                    SerializerInterface::class
                ))
            ;
        }

        return $this->serializer;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getEntityClass(): string
    {
        if (null === $this->entityClass) {
            throw new \RuntimeException(
                'ODM entity class name must be available. Call the "setEntityClass" method.'
            );
        }

        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

    public function getName(): string
    {
        return $this->getEntityClass();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return $this->getSerializer()->serialize($value, $this->format);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?object
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return $this->getSerializer()->deserialize($value, $this->getEntityClass(), $this->format);
    }

    public static function registerODMType(string $entityClass, SerializerInterface $serializer): void
    {
        if (!class_exists($entityClass)) {
            throw new \DomainException(sprintf('Class "%s" does not exist.', $entityClass));
        }

        self::addType($entityClass, static::class);

        /** @var ODMType $type */
        $type = self::getType($entityClass);
        $type->setEntityClass($entityClass);
        $type->setSerializer($serializer);
    }
}
