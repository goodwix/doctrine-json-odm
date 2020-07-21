<?php

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntity;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyEntityMap;
use Goodwix\DoctrineJsonOdm\Tests\Resources\DummyPrimitiveMap;
use Goodwix\DoctrineJsonOdm\Tests\TestCase\SerializerTestCase;
use Ramsey\Collection\Map\TypedMapInterface;

class MapSerializationTest extends SerializerTestCase
{
    /**
     * @test
     * @dataProvider mapAndExpectedJsonProvider
     */
    public function serialize_map_encodedToJson(TypedMapInterface $map, string $expectedJson): void
    {
        $json = $this->serializer->serialize($map, self::JSON_FORMAT);

        $this->assertSame($expectedJson, $json);
    }

    public function mapAndExpectedJsonProvider(): \Iterator
    {
        yield 'primitive map' => [new DummyPrimitiveMap(['key' => 'value']), '{"key":"value"}'];
        yield 'entity map' => [new DummyEntityMap(['key' => new DummyEntity()]), '{"key":{}}'];
        yield 'empty map' => [new DummyPrimitiveMap(), '{}'];
    }
}
