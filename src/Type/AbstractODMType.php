<?php
declare(strict_types=1);


namespace Goodwix\DoctrineJsonOdm\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Goodwix\DoctrineJsonOdm\Exception\JsonOdmException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;


abstract class AbstractODMType extends JsonType
{
    /** @var array */
    public $deserializationContext = [];
    /** @var SerializerInterface */
    private $serializer;

    /** @var string */
    private $format = 'json';

    /** @var string */
    private $entityClass;

    /** @var array */
    private $serializationContext = [];

    public function getSerializer(): SerializerInterface
    {
        if (null === $this->serializer) {
            throw new \RuntimeException(
                sprintf(
                    'An instance of "%s" must be available. Call the "setSerializer" method.',
                    SerializerInterface::class
                ));
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

    public function getSerializationContext(): array
    {
        return $this->serializationContext;
    }

    public function setSerializationContext(array $serializationContext): void
    {
        $this->serializationContext = $serializationContext;
    }

    public function getDeserializationContext(): array
    {
        return $this->deserializationContext;
    }

    public function setDeserializationContext(array $deserializationContext): void
    {
        $this->deserializationContext = $deserializationContext;
    }

    public function getName(): string
    {
        return $this->getEntityClass();
    }

    /**
     * @throws JsonOdmException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        try {
            $context = $this->getSerializationContext();
            $value = $this->getSerializer()->serialize($value, $this->format, $context);
        } catch (ExceptionInterface $exception) {
            $message = sprintf('Serialization exception occurred for class "%s".', $this->getEntityClass());

            throw new JsonOdmException($message, 0, $exception);
        }

        return $value;
    }

    /**
     * @throws JsonOdmException
     * @return object|null|array
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            $context = $this->getDeserializationContext();
            $value = $this->getSerializer()->deserialize($value, $this->getEntityClass(), $this->format, $context);
        } catch (ExceptionInterface $exception) {
            $message = sprintf('Deserialization exception occurred for class "%s".', $this->getEntityClass());

            throw new JsonOdmException($message, 0, $exception);
        }

        return $value;
    }

    public static abstract function registerODMType(string $entityClass, SerializerInterface $serializer): void;
}