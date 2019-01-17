<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Unit\Service;

use Goodwix\DoctrineJsonOdm\Service\ODMAutoRegistrar;
use Goodwix\DoctrineJsonOdm\Type\ODMType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ODMAutoRegistrarTest extends TestCase
{
    /** @var SerializerInterface */
    private $serializer;

    protected function setUp(): void
    {
        $this->serializer = \Phake::mock(SerializerInterface::class);
    }

    /** @test */
    public function registerODMTypes_entityClass_odmTypeRegistered(): void
    {
        $registrar = new ODMAutoRegistrar($this->serializer, [self::class]);

        $registrar->registerODMTypes();

        $this->assertTrue(ODMType::hasType(self::class));
    }
}
