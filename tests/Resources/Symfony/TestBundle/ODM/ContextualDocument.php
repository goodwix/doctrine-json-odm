<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM;

use Goodwix\DoctrineJsonOdm\Annotation\ODM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ODM(
 *     serializationContext={
 *          "groups"={"toDatabase"}
 *     },
 *     deserializationContext={
 *          "groups"={"fromDatabase"}
 *     }
 * )
 */
class ContextualDocument
{
    /**
     * @Groups({"fromDatabase", "toDatabase"})
     *
     * @var string
     */
    public $title;

    /** @var string */
    private $readOnlyProperty;

    public function __construct(string $title, string $readOnlyProperty = '')
    {
        $this->title            = $title;
        $this->readOnlyProperty = $readOnlyProperty;
    }

    public function getReadOnlyProperty(): string
    {
        return $this->readOnlyProperty;
    }

    public function getGeneratedProperty(): string
    {
        return 'generatedValue';
    }
}
