<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Unit\Serialization\RamseyCollection;

use Goodwix\DoctrineJsonOdm\Serialization\RamseyCollection\CollectionNormalizer;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntity;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntityCollection;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyPrimitiveCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CollectionNormalizerTest extends TestCase
{
    /** @var DenormalizerInterface */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = \Phake::mock(DenormalizerInterface::class);
    }

    /** @test */
    public function supportsDenormalization_arrayAndClassInheritsCollectionInterfaceType_trueReturned(): void
    {
        $normalizer = $this->createCollectionNormalizer();

        $supports = $normalizer->supportsDenormalization([], DummyEntityCollection::class);

        $this->assertTrue($supports);
    }

    /** @test */
    public function supportsDenormalization_stringAndClassInheritsCollectionInterfaceType_trueReturned(): void
    {
        $normalizer = $this->createCollectionNormalizer();

        $supports = $normalizer->supportsDenormalization('', DummyEntityCollection::class);

        $this->assertFalse($supports);
    }

    /** @test */
    public function supportsDenormalization_notAClassName_falseReturned(): void
    {
        $normalizer = $this->createCollectionNormalizer();

        $supports = $normalizer->supportsDenormalization([], 'Not\\A\\Class[]');

        $this->assertFalse($supports);
    }

    /** @test */
    public function denormalize_arrayOfCollectionEntity_entityCollectionReturned(): void
    {
        $normalizer = $this->createCollectionNormalizer();
        $data       = [
            [
                'id' => 'id',
            ],
        ];
        $this->givenDenormalizer_denormalize_returnItem(new DummyEntity());

        $collection = $normalizer->denormalize($data, DummyEntityCollection::class, 'json');

        $this->assertCount(1, $collection);
        $this->assertInstanceOf(DummyEntity::class, $collection->first());
        $this->assertDenormalizer_denormalize_wasCalledOnceWithDataAndType($data[0], DummyEntity::class);
    }

    /** @test */
    public function denormalize_arrayOfPrimitive_primitiveCollectionReturned(): void
    {
        $normalizer = $this->createCollectionNormalizer();
        $data       = [
            'value1',
            'value2',
            'value3',
        ];

        $collection = $normalizer->denormalize($data, DummyPrimitiveCollection::class, 'json');

        $this->assertCount(3, $collection);
        $this->assertSame($data, $collection->toArray());
        $this->assertDenormalizer_denormalize_wasNeverCalled();
    }

    /** @test */
    public function denormalize_string_exceptionThrown(): void
    {
        $normalizer = $this->createCollectionNormalizer();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected value of type "array", value of type "string" is given.');

        $normalizer->denormalize('', DummyPrimitiveCollection::class, 'json');
    }

    private function createCollectionNormalizer(): CollectionNormalizer
    {
        $normalizer = new CollectionNormalizer();
        $normalizer->setDenormalizer($this->denormalizer);

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
            ->denormalize($data, $type, 'json', []);
    }

    private function assertDenormalizer_denormalize_wasNeverCalled(): void
    {
        \Phake::verify($this->denormalizer, \Phake::never())
            ->denormalize(\Phake::anyParameters());
    }
}
