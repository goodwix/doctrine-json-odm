<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Goodwix\DoctrineJsonOdm\Bridge\Symfony\DoctrineJsonOdmBundle;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\TestBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DoctrineJsonOdmBundle(),
            new TestBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'secret' => 's$cretf0rt3st',
            'test'   => true,
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'driver'   => 'pdo_pgsql',
                'host'     => getenv('DATABASE_HOST'),
                'dbname'   => getenv('DATABASE_DBNAME'),
                'user'     => getenv('DATABASE_USER'),
                'password' => getenv('DATABASE_PASSWORD'),
                'charset'  => 'UTF8',
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'auto_mapping'                => true,
            ],
        ]);

        $container->loadFromExtension('doctrine_json_odm', [
            'mapping' => [
                'paths' => [
                    __DIR__.'/TestBundle/ODM',
                ],
            ],
        ]);
    }
}
