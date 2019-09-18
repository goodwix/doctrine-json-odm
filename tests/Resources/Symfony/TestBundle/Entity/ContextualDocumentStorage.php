<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\ContextualDocument;

/**
 * @ORM\Entity()
 */
class ContextualDocumentStorage
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(type=ContextualDocument::class, nullable=false)
     *
     * @var ContextualDocument
     */
    public $document;
}
