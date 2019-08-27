<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Resources;

use Ramsey\Collection\AbstractCollection;

class DummyEntityInterfaceCollection extends AbstractCollection
{
    public function getType(): string
    {
        return DummyEntityInterface::class;
    }
}
