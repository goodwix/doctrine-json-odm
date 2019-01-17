<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Service;

use Goodwix\DoctrineJsonOdm\Type\ODMType;
use Symfony\Component\Serializer\SerializerInterface;

class ODMAutoRegistrar
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var string[] */
    private $entityClassList;

    public function __construct(SerializerInterface $serializer, array $entityClassList)
    {
        $this->serializer      = $serializer;
        $this->entityClassList = $entityClassList;
    }

    public function registerODMTypes(): void
    {
        foreach ($this->entityClassList as $entityClass) {
            if (!ODMType::hasType($entityClass)) {
                ODMType::registerODMType($entityClass, $this->serializer);
            }
        }
    }
}
