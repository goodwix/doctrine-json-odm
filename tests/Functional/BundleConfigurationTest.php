<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Bridge\Symfony\DependencyInjection\DoctrineJsonOdmExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BundleConfigurationTest extends TestCase
{
    private const VALID_CONFIGURATION = [
        'mapping' => [
            'paths' => [
                'path',
            ],
        ],
    ];
    private const EMPTY_CONFIGURATION              = [];
    private const CONFIGURATION_WITH_EMPTY_MAPPING = [
        'mapping' => [],
    ];
    private const CONFIGURATION_WITH_EMPTY_MAPPING_PATHS = [
        'mapping' => [
            'paths' => [],
        ],
    ];

    /** @test */
    public function load_validConfiguration_noExceptions(): void
    {
        $extension = new DoctrineJsonOdmExtension();
        $builder   = new ContainerBuilder();

        $extension->load([self::VALID_CONFIGURATION], $builder);

        $this->assertTrue(true);
    }

    /**
     * @test
     * @dataProvider invalidConfigurationProvider
     */
    public function load_invalidConfiguration_exceptionThrown(array $configuration): void
    {
        $extension = new DoctrineJsonOdmExtension();
        $builder   = new ContainerBuilder();

        $this->expectException(InvalidConfigurationException::class);

        $extension->load([$configuration], $builder);
    }

    public function invalidConfigurationProvider(): array
    {
        return [
            [self::EMPTY_CONFIGURATION],
            [self::CONFIGURATION_WITH_EMPTY_MAPPING],
            [self::CONFIGURATION_WITH_EMPTY_MAPPING_PATHS],
        ];
    }
}
