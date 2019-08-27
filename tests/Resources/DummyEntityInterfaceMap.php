<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Goodwix\DoctrineJsonOdm\Tests\Resources;


use Ramsey\Collection\Map\AbstractTypedMap;

class DummyEntityInterfaceMap extends AbstractTypedMap
{
    public function getKeyType(): string
    {
        return 'string';
    }

    public function getValueType(): string
    {
        return DummyEntityInterface::class;
    }

}