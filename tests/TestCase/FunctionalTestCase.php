<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\TestCase;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;

class FunctionalTestCase extends KernelTestCase
{
    /** @var Application */
    private $application;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->application = new Application(self::$kernel);
        $this->application->setAutoExit(false);

        $this->runCommand('doctrine:schema:drop --force');
        $this->runCommand('doctrine:schema:create');
    }

    protected function getConnection(): Connection
    {
        $container = KernelTestCase::$kernel->getContainer();
        $doctrine  = $container->get('doctrine');

        return $doctrine->getConnection();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $container = KernelTestCase::$kernel->getContainer();
        $doctrine  = $container->get('doctrine');

        return $doctrine->getManager();
    }

    protected function assertTableColumnEqualsToJson(string $entityClass, string $entityField, string $json): void
    {
        $manager = $this->getEntityManager();

        $classMetadata = $manager->getClassMetadata($entityClass);
        $columnName    = $classMetadata->fieldMappings[$entityField]['columnName'];
        $tableName     = $classMetadata->getTableName();
        $sql           = \sprintf('SELECT %s FROM %s', $columnName, $tableName);

        $connection = $this->getConnection();
        $statement  = $connection->prepare($sql);
        $column = $statement->executeQuery()->fetchFirstColumn()[0];

        $cleanedJson = json_encode(json_decode($json));
        $this->assertSame($cleanedJson, $column);
    }

    private function runCommand(string $command): int
    {
        return $this->application->run(new StringInput($command.' --no-interaction --quiet'));
    }
}
