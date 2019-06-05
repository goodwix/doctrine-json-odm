<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Bridge\Symfony\DependencyInjection;

use Ramsey\Collection\CollectionInterface;
use Ramsey\Collection\Map\TypedMapInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class DoctrineJsonOdmExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $frameworkConfiguration = $container->getExtensionConfig('framework');

        if (empty($frameworkConfiguration)) {
            return;
        }

        if (!isset($frameworkConfiguration['serializer']['enabled'])) {
            $container->prependExtensionConfig('framework', ['serializer' => ['enabled' => true]]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->loadServices($container);
        $this->prepareConfiguration($configs, $container);
    }

    private function loadServices(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (
            interface_exists(CollectionInterface::class)
            && interface_exists(TypedMapInterface::class)
        ) {
            $loader->load('ramsey_collection_normalizers.xml');
        }
    }

    private function prepareConfiguration(array $configs, ContainerBuilder $container): void
    {
        $configuration          = new Configuration();
        $processedConfiguration = $this->processConfiguration($configuration, $configs);

        $container->setParameter(ODMTypeCompilerPass::ODM_PATHS, $processedConfiguration['mapping']['paths']);
    }
}
