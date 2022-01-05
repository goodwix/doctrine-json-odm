<?php

namespace Goodwix\DoctrineJsonOdm\Tests\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerTestCase extends KernelTestCase
{
    protected const JSON_FORMAT = 'json';

    /** @var SerializerInterface */
    protected $serializer;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = static::getContainer();

        $this->serializer = $container->get('serializer');
    }
}
