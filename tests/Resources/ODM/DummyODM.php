<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Resources\ODM;

use Goodwix\DoctrineJsonOdm\Annotation\ODM;

/**
 * @ODM()
 */
class DummyODM
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;
}
