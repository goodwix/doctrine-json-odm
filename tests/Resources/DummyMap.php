<?php

namespace Goodwix\DoctrineJsonOdm\Tests\Resources;

use Ramsey\Collection\Map\AbstractTypedMap;
use Ramsey\Collection\Map\MapInterface;

class DummyMap extends AbstractTypedMap
{
    public function getKeyType(): string
    {
        return 'string';
    }

    public function getValueType(): string
    {
        return MapInterface::class;
    }
}
