<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM;

use Goodwix\DoctrineJsonOdm\Annotation\ODM;
use Ramsey\Collection\Map\AbstractTypedMap;

/**
 * @ODM()
 */
class DocumentMap extends AbstractTypedMap
{
    public function getKeyType(): string
    {
        return 'string';
    }

    public function getValueType(): string
    {
        return Document::class;
    }
}
