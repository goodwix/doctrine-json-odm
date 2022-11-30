<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Unit\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Goodwix\DoctrineJsonOdm\Exception\JsonOdmException;
use Goodwix\DoctrineJsonOdm\Tests\Resources\ODM\DummyODM;
use Goodwix\DoctrineJsonOdm\Tests\Resources\ODMInterface\DummyODMInterface;
use Goodwix\DoctrineJsonOdm\Type\ODMType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ODMTypeTest extends TestCase
{
    private const ODM_TYPE_NAME    = '_odm_type_name';
    private const JSON_FORMAT      = 'json';
    private const CUSTOM_FORMAT    = 'custom_format';
    private const SERIALIZED_VALUE = 'serialized_value';
    private const ENTITY_CLASS     = 'entity_class';
    private const CONTEXT          = ['context'];

    /** @var SerializerInterface */
    private $serializer;

    /** @var AbstractPlatform */
    private $platform;

    protected function setUp(): void
    {
        $this->serializer = \Phake::mock(SerializerInterface::class);
        $this->platform   = \Phake::mock(AbstractPlatform::class);
    }

    /** @test */
    public function getSerializer_serializerIsSet_serializerReturned(): void
    {
        $type = $this->createODMType();
        $type->setSerializer($this->serializer);

        $serializer = $type->getSerializer();

        $this->assertSame($this->serializer, $serializer);
    }

    /** @test */
    public function getSerializer_noSerializerIsSet_exceptionThrown(): void
    {
        $type = $this->createODMType();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/An instance of .* must be available. Call the "setSerializer" method./');

        $type->getSerializer();
    }

    /** @test */
    public function getFormat_noCustomFormatIsSet_jsonAsDefaultFormatReturned(): void
    {
        $type = $this->createODMType();

        $format = $type->getFormat();

        $this->assertSame(self::JSON_FORMAT, $format);
    }

    /** @test */
    public function getFormat_customFormatIsSet_customFormatReturned(): void
    {
        $type = $this->createODMType();
        $type->setFormat(self::CUSTOM_FORMAT);

        $format = $type->getFormat();

        $this->assertSame(self::CUSTOM_FORMAT, $format);
    }

    /** @test */
    public function getEntityClass_entityClassIsSet_entityClassReturned(): void
    {
        $type = $this->createODMType();
        $type->setEntityClass(self::class);

        $entityClass = $type->getEntityClass();

        $this->assertSame(self::class, $entityClass);
    }

    /** @test */
    public function getEntityClass_noEntityClassIsSet_exceptionThrown(): void
    {
        $type = $this->createODMType();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ODM entity class name must be available. Call the "setEntityClass" method.');

        $type->getEntityClass();
    }

    /** @test */
    public function getSerializationContext_context_contextReturned(): void
    {
        $type = $this->createODMType();
        $type->setSerializationContext(self::CONTEXT);

        $context = $type->getSerializationContext();

        $this->assertSame(self::CONTEXT, $context);
    }

    /** @test */
    public function getDeserializationContext_context_contextReturned(): void
    {
        $type = $this->createODMType();
        $type->setDeserializationContext(self::CONTEXT);

        $context = $type->getDeserializationContext();

        $this->assertSame(self::CONTEXT, $context);
    }

    /** @test */
    public function getName_entityClassIsSet_entityClassReturnedAsName(): void
    {
        $type = $this->createODMType();
        $type->setEntityClass(self::class);

        $entityClass = $type->getName();

        $this->assertSame(self::class, $entityClass);
    }

    /** @test */
    public function convertToDatabaseValue_object_objectSerializedAndReturned(): void
    {
        $type = $this->createODMType();
        $type->setSerializer($this->serializer);
        $type->setSerializationContext(self::CONTEXT);
        $object = new DummyODM();
        $this->givenSerialize_serialize_returnsValue(self::SERIALIZED_VALUE);

        $value = $type->convertToDatabaseValue($object, $this->platform);

        $this->assertSerializer_serialize_wasCalledOnceWithObjectAndFormatAndContext(
            $object,
            self::JSON_FORMAT,
            self::CONTEXT
        );
        $this->assertSame(self::SERIALIZED_VALUE, $value);
    }

    /** @test */
    public function convertToDatabaseValue_serializerException_exceptionConvertedToJsonOdmException(): void
    {
        $type = $this->createODMType();
        $type->setEntityClass(self::ENTITY_CLASS);
        $type->setSerializer($this->serializer);
        $object              = new DummyODM();
        $serializerException = $this->givenSerializerException();
        $this->givenSerialize_serialize_throwsException($serializerException);
        $exception = null;

        try {
            $type->convertToDatabaseValue($object, $this->platform);
        } catch (\Throwable $exception) {
        }

        $this->assertSerializer_serialize_wasCalledOnceWithObjectAndFormatAndContext($object, self::JSON_FORMAT);
        $this->assertInstanceOf(JsonOdmException::class, $exception);
        $this->assertSame('Serialization exception occurred for class "entity_class": internal exception.', $exception->getMessage());
        $this->assertSame($serializerException, $exception->getPrevious());
    }

    /** @test */
    public function convertToDatabaseValue_null_nullReturned(): void
    {
        $type = $this->createODMType();
        $type->setSerializer($this->serializer);

        $value = $type->convertToDatabaseValue(null, $this->platform);

        $this->assertNull($value);
    }

    /** @test */
    public function convertToPHPValue_nonEmptyString_deserializedObjectReturned(): void
    {
        $type = $this->createODMType();
        $type->setSerializer($this->serializer);
        $type->setEntityClass(self::ENTITY_CLASS);
        $type->setDeserializationContext(self::CONTEXT);
        $object = $this->givenSerializer_deserialize_returnsObject();

        $value = $type->convertToPHPValue(self::SERIALIZED_VALUE, $this->platform);

        $this->assertSerializer_deserialize_wasCalledOnceWithValueAndTypeAndFormatAndContext(
            self::SERIALIZED_VALUE,
            self::ENTITY_CLASS,
            self::JSON_FORMAT,
            self::CONTEXT
        );
        $this->assertSame($object, $value);
    }

    /** @test */
    public function convertToPHPValue_serializerException_exceptionConvertedToJsonOdmException(): void
    {
        $type = $this->createODMType();
        $type->setSerializer($this->serializer);
        $type->setEntityClass(self::ENTITY_CLASS);
        $serializerException = $this->givenSerializerException();
        $this->givenSerializer_deserialize_throwsException($serializerException);
        $exception = null;

        try {
            $type->convertToPHPValue(self::SERIALIZED_VALUE, $this->platform);
        } catch (\Throwable $exception) {
        }

        $this->assertSerializer_deserialize_wasCalledOnceWithValueAndTypeAndFormatAndContext(
            self::SERIALIZED_VALUE,
            self::ENTITY_CLASS,
            self::JSON_FORMAT
        );
        $this->assertInstanceOf(JsonOdmException::class, $exception);
        $this->assertSame('Deserialization exception occurred for class "entity_class": internal exception.', $exception->getMessage());
        $this->assertSame($serializerException, $exception->getPrevious());
    }

    /**
     * @test
     * @dataProvider emptyValueProvider
     */
    public function convertToPHPValue_nullOrEmptyString_nullReturned(?string $value): void
    {
        $type = $this->createODMType();

        $value = $type->convertToPHPValue($value, $this->platform);

        $this->assertNull($value);
    }

    /** @test */
    public function registerODMType_entityClassAndSerializer_doctrineTypeRegisteredWithEntityClassAndSerializer(): void
    {
        ODMType::registerODMType(DummyODM::class, $this->serializer);
        /** @var ODMType $type */
        $type = ODMType::getType(DummyODM::class);

        $this->assertTrue(ODMType::hasType(DummyODM::class));
        $this->assertSame(DummyODM::class, $type->getEntityClass());
        $this->assertSame($this->serializer, $type->getSerializer());
    }

    /** @test */
    public function registerODMType_entityInterfaceAndSerializer_doctrineTypeRegisteredWithEntityClassAndSerializer(): void
    {
        ODMType::registerODMType(DummyODMInterface::class, $this->serializer);
        /** @var ODMType $type */
        $type = ODMType::getType(DummyODMInterface::class);

        $this->assertTrue(ODMType::hasType(DummyODMInterface::class));
        $this->assertSame(DummyODMInterface::class, $type->getEntityClass());
        $this->assertSame($this->serializer, $type->getSerializer());
    }

    /** @test */
    public function registerODMType_entityClassIsNotAClass_exceptionThrown(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Class or interface "not_a_class" does not exist.');

        ODMType::registerODMType('not_a_class', $this->serializer);
    }

    public function emptyValueProvider(): array
    {
        return [
            [null],
            [''],
        ];
    }

    private function createODMType(): ODMType
    {
        if (ODMType::hasType(self::ODM_TYPE_NAME)) {
            ODMType::overrideType(self::ODM_TYPE_NAME, ODMType::class);
        } else {
            ODMType::addType(self::ODM_TYPE_NAME, ODMType::class);
        }

        return ODMType::getType(self::ODM_TYPE_NAME);
    }

    private function assertSerializer_serialize_wasCalledOnceWithObjectAndFormatAndContext(
        object $object,
        string $format,
        array $context = []
    ): void {
        \Phake::verify($this->serializer)
            ->serialize($object, $format, $context);
    }

    private function givenSerialize_serialize_returnsValue($value): void
    {
        \Phake::when($this->serializer)
            ->serialize(\Phake::anyParameters())
            ->thenReturn($value);
    }

    private function givenSerialize_serialize_throwsException(\Throwable $exception): void
    {
        \Phake::when($this->serializer)
            ->serialize(\Phake::anyParameters())
            ->thenThrow($exception);
    }

    private function assertSerializer_deserialize_wasCalledOnceWithValueAndTypeAndFormatAndContext(
        string $value,
        string $type,
        string $format,
        array $context = []
    ): void {
        \Phake::verify($this->serializer)
            ->deserialize($value, $type, $format, $context);
    }

    private function givenSerializer_deserialize_returnsObject(): DummyODM
    {
        $object = new DummyODM();

        \Phake::when($this->serializer)
            ->deserialize(\Phake::anyParameters())
            ->thenReturn($object);

        return $object;
    }

    private function givenSerializer_deserialize_throwsException(\Throwable $exception): void
    {
        \Phake::when($this->serializer)
            ->deserialize(\Phake::anyParameters())
            ->thenThrow($exception);
    }

    private function givenSerializerException(): ExceptionInterface
    {
        return new class() extends \Exception implements ExceptionInterface {
            protected $message = 'internal exception';
        };
    }
}
