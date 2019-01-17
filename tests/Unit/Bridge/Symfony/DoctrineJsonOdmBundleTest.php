<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Unit\Bridge\Symfony;

use Goodwix\DoctrineJsonOdm\Bridge\Symfony\DependencyInjection\ODMTypeCompilerPass;
use Goodwix\DoctrineJsonOdm\Bridge\Symfony\DoctrineJsonOdmBundle;
use Goodwix\DoctrineJsonOdm\Service\ODMAutoRegistrar;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineJsonOdmBundleTest extends TestCase
{
    /** @var ContainerBuilder */
    private $container;

    /** @var ODMAutoRegistrar */
    private $registrar;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerBuilder::class);
        $this->registrar = \Phake::mock(ODMAutoRegistrar::class);

        \Phake::when($this->container)
            ->get(ODMTypeCompilerPass::ODM_AUTO_REGISTRAR)
            ->thenReturn($this->registrar);
    }

    /** @test */
    public function build_container_odmTypeCompilerPassAdded(): void
    {
        $bundle = new DoctrineJsonOdmBundle();

        $bundle->build($this->container);

        $this->assertContainer_addCompilerPass_wasCalledOnceWithInstanceOfClass(ODMTypeCompilerPass::class);
    }

    /** @test */
    public function boot_noParameters_odmTypesRegistrationInvoked(): void
    {
        $bundle = new DoctrineJsonOdmBundle();
        $bundle->setContainer($this->container);

        $bundle->boot();

        $this->assertContainer_get_wasCalledOnceWithId(ODMTypeCompilerPass::ODM_AUTO_REGISTRAR);
        $this->assertOdmAutoRegistrar_registerODMTypes_wasCalledOnce();
    }

    private function assertContainer_addCompilerPass_wasCalledOnceWithInstanceOfClass(string $class): void
    {
        \Phake::verify($this->container)
            ->addCompilerPass(\Phake::capture($pass));
        $this->assertInstanceOf($class, $pass);
    }

    private function assertContainer_get_wasCalledOnceWithId(string $id): void
    {
        \Phake::verify($this->container)
            ->get($id);
    }

    private function assertOdmAutoRegistrar_registerODMTypes_wasCalledOnce(): void
    {
        \Phake::verify($this->registrar)
            ->registerODMTypes();
    }
}
