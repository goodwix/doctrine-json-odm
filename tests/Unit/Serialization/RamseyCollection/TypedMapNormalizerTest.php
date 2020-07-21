<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Unit\Serialization\RamseyCollection;

use Goodwix\DoctrineJsonOdm\Serialization\RamseyCollection\TypedMapNormalizer;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntity;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntityInterface;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntityInterfaceMap;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntityMap;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyPrimitiveMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TypedMapNormalizerTest extends TestCase
{
    protected const JSON_FORMAT = 'json';
    protected const KEY         = 'key';
    protected const VALUE       = 'value';

    /** @var DenormalizerInterface */
    private $denormalizer;

    /** @var NormalizerInterface */
    private $normalizer;

    protected function setUp(): void
    {
        $this->denormalizer = \Phake::mock(DenormalizerInterface::class);
        $this->normalizer   = \Phake::mock(NormalizerInterface::class);
    }

    /** @test */
    public function supportsDenormalization_arrayAndClassInheritsCollectionInterfaceType_trueReturned(): void
    {
        $normalizer = $this->createMapNormalizer();

        $supports = $normalizer->supportsDenormalization([], DummyEntityMap::class);

        $this->assertTrue($supports);
    }

    /** @test */
    public function supportsDenormalization_stringAndClassInheritsCollectionInterfaceType_trueReturned(): void
    {
        $normalizer = $this->createMapNormalizer();

        $supports = $normalizer->supportsDenormalization('', DummyEntityMap::class);

        $this->assertFalse($supports);
    }

    /** @test */
    public function supportsDenormalization_notAClassName_falseReturned(): void
    {
        $normalizer = $this->createMapNormalizer();

        $supports = $normalizer->supportsDenormalization([], 'Not\\A\\Class[]');

        $this->assertFalse($supports);
    }

    /** @test */
    public function denormalize_arrayOfClassMap_classMapReturned(): void
    {
        $normalizer = $this->createMapNormalizer();
        $data       = [
            self::KEY => [
                'id',
            ],
        ];
        $this->givenDenormalizer_denormalize_returnItem(new DummyEntity());

        $map = $normalizer->denormalize($data, DummyEntityMap::class, self::JSON_FORMAT);

        $this->assertCount(1, $map);
        $this->assertInstanceOf(DummyEntity::class, $map->get(self::KEY));
        $this->assertDenormalizer_denormalize_wasCalledOnceWithDataAndType($data[self::KEY], DummyEntity::class);
    }

    /** @test */
    public function denormalize_arrayOfInterfaceMap_interfaceMapReturned(): void
    {
        $normalizer = $this->createMapNormalizer();
        $data       = [
            self::KEY => [
                'id',
            ],
        ];
        $this->givenDenormalizer_denormalize_returnItem(\Phake::mock(DummyEntityInterface::class));

        $map = $normalizer->denormalize($data, DummyEntityInterfaceMap::class, self::JSON_FORMAT);

        $this->assertCount(1, $map);
        $this->assertInstanceOf(DummyEntityInterface::class, $map->get(self::KEY));
        $this->assertDenormalizer_denormalize_wasCalledOnceWithDataAndType($data[self::KEY], DummyEntityInterface::class);
    }

    /** @test */
    public function denormalize_arrayOfPrimitive_primitiveCollectionReturned(): void
    {
        $normalizer = $this->createMapNormalizer();
        $data       = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $collection = $normalizer->denormalize($data, DummyPrimitiveMap::class, self::JSON_FORMAT);

        $this->assertCount(3, $collection);
        $this->assertSame($data, $collection->toArray());
        $this->assertDenormalizer_denormalize_wasNeverCalled();
    }

    /** @test */
    public function denormalize_string_exceptionThrown(): void
    {
        $normalizer = $this->createMapNormalizer();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected value of type "array", value of type "string" is given.');

        $normalizer->denormalize('', DummyPrimitiveMap::class, self::JSON_FORMAT);
    }

    /** @test */
    public function denormalize_arrayOfClassCollectionAndInvalidItem_invalidArgumentException(): void
    {
        $normalizer = $this->createMapNormalizer();
        $data       = [
            'id' => 'id',
        ];
        $this->givenDenormalizer_denormalize_returnItem(new \stdClass());

        $this->expectException(InvalidArgumentException::class);

        $normalizer->denormalize($data, DummyEntityMap::class, self::JSON_FORMAT);
    }

    /**
     * @test
     */
    public function supportsNormalization_notATypedMapInterface_falseReturned(): void
    {
        $normalizer = $this->createMapNormalizer();

        $supports = $normalizer->supportsNormalization(new \stdClass());

        $this->assertFalse($supports);
    }

    /**
     * @test
     */
    public function supportsNormalization_typedMapInterface_trueReturned(): void
    {
        $normalizer = $this->createMapNormalizer();

        $supports = $normalizer->supportsNormalization(new DummyPrimitiveMap());

        $this->assertTrue($supports);
    }

    /** @test */
    public function normalize_emptyMap_arrayObjectReturned(): void
    {
        $normalizer = $this->createMapNormalizer();

        $map = $normalizer->normalize(new DummyPrimitiveMap());

        $this->assertInstanceOf(\ArrayObject::class, $map);
        $this->assertNormalizer_normalize_wasNeverCalled();
    }

    /** @test */
    public function normalize_map_arrayReturned(): void
    {
        $normalizer    = $this->createMapNormalizer();
        $expectedArray = [self::KEY => self::VALUE];
        $map           = new DummyPrimitiveMap($expectedArray);
        $this->givenNormalizer_normalize_returnData(self::VALUE);

        $normalizedMap = $normalizer->normalize($map, self::JSON_FORMAT);

        $this->assertIsArray($normalizedMap);
        $this->assertSame($expectedArray, $normalizedMap);
        $this->assertNormalizer_normalize_wasCalledOnceWithObject(self::VALUE);
    }

    private function createMapNormalizer(): TypedMapNormalizer
    {
        $normalizer = new TypedMapNormalizer();
        $normalizer->setDenormalizer($this->denormalizer);
        $normalizer->setNormalizer($this->normalizer);

        return $normalizer;
    }

    private function givenDenormalizer_denormalize_returnItem($item): void
    {
        \Phake::when($this->denormalizer)
            ->denormalize(\Phake::anyParameters())
            ->thenReturn($item);
    }

    private function assertDenormalizer_denormalize_wasCalledOnceWithDataAndType(array $data, string $type): void
    {
        \Phake::verify($this->denormalizer)
            ->denormalize($data, $type, self::JSON_FORMAT, []);
    }

    private function assertDenormalizer_denormalize_wasNeverCalled(): void
    {
        \Phake::verify($this->denormalizer, \Phake::never())
            ->denormalize(\Phake::anyParameters());
    }

    private function givenNormalizer_normalize_returnData($data): void
    {
        \Phake::when($this->normalizer)
            ->normalize(\Phake::anyParameters())
            ->thenReturn($data);
    }

    private function assertNormalizer_normalize_wasCalledOnceWithObject($object): void
    {
        \Phake::verify($this->normalizer)
            ->normalize($object, self::JSON_FORMAT, []);
    }

    private function assertNormalizer_normalize_wasNeverCalled(): void
    {
        \Phake::verify($this->normalizer, \Phake::never())
            ->normalize(\Phake::anyParameters());
    }
}
