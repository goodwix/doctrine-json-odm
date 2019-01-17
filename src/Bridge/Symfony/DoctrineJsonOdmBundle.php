<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Bridge\Symfony;

use Goodwix\DoctrineJsonOdm\Bridge\Symfony\DependencyInjection\ODMTypeCompilerPass;
use Goodwix\DoctrineJsonOdm\Service\ODMAutoRegistrar;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineJsonOdmBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ODMTypeCompilerPass());
    }

    public function boot(): void
    {
        /** @var ODMAutoRegistrar $odmAutoRegistrar */
        $odmAutoRegistrar = $this->container->get(ODMTypeCompilerPass::ODM_AUTO_REGISTRAR);
        $odmAutoRegistrar->registerODMTypes();
    }
}
