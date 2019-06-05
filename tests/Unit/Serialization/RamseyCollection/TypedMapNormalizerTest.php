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
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntityMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class TypedMapNormalizerTest extends TestCase
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
    public function denormalize_arrayOfCollectionEntity_entityCollectionReturned(): void
    {
        $normalizer = $this->createMapNormalizer();
        $data       = [
            'key' => [
                'id',
            ],
        ];
        $this->givenDenormalizer_denormalize_returnItem(new DummyEntity());

        $map = $normalizer->denormalize($data, DummyEntityMap::class, 'json');

        $this->assertCount(1, $map);
        $this->assertInstanceOf(DummyEntity::class, $map->get('key'));
        $this->assertDenormalizer_denormalize_wasCalledOnceWithDataAndType($data['key'], DummyEntity::class);
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

    private function createMapNormalizer(): TypedMapNormalizer
    {
        $normalizer = new TypedMapNormalizer();
        $normalizer->setDenormalizer($this->denormalizer);

        return $normalizer;
    }
}
