<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Unit\Bridge\Symfony\DependencyInjection;

use Goodwix\DoctrineJsonOdm\Bridge\Symfony\DependencyInjection\DoctrineJsonOdmExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineJsonOdmExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    private $container;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerBuilder::class);
    }

    /** @test */
    public function prepend_noFrameworkConfiguration_noInteractionWithContainer(): void
    {
        $extension = new DoctrineJsonOdmExtension();
        $this->givenContainer_getExtensionConfig_returnsConfiguration([]);

        $extension->prepend($this->container);

        $this->assertContainer_getExtensionConfig_wasCalledOnceWithName('framework');
        \Phake::verifyNoFurtherInteraction($this->container);
    }

    /** @test */
    public function prepend_frameworkConfiguration_serializerEnabled(): void
    {
        $extension = new DoctrineJsonOdmExtension();
        $this->givenContainer_getExtensionConfig_returnsConfiguration([
            'serializer' => [],
        ]);

        $extension->prepend($this->container);

        $this->assertContainer_getExtensionConfig_wasCalledOnceWithName('framework');
        $this->assertContainer_prependExtensionConfig_wasCalledOnceWithNameAndConfiguration(
            'framework',
            ['serializer' => ['enabled' => true]]
        );
    }

    private function assertContainer_getExtensionConfig_wasCalledOnceWithName(string $name): void
    {
        \Phake::verify($this->container)
            ->getExtensionConfig($name);
    }

    private function givenContainer_getExtensionConfig_returnsConfiguration(array $configuration): void
    {
        \Phake::when($this->container)
            ->getExtensionConfig(\Phake::anyParameters())
            ->thenReturn($configuration);
    }

    private function assertContainer_prependExtensionConfig_wasCalledOnceWithNameAndConfiguration(string $name, array $configuration): void
    {
        \Phake::verify($this->container)
            ->prependExtensionConfig($name, $configuration);
    }
}
