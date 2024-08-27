<?php

declare(strict_types=1);

namespace App\Tests\TestCase;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\Connection;

class IntegrationTestCase extends KernelTestCase
{
    protected Connection $connection;
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $this->connection = $entityManager->getConnection();
        $params = $this->connection->getParams();
        $isSqliteInMemory = $params['driver'] === 'pdo_sqlite' && $params['path'] === ':memory:';

        if ($isSqliteInMemory) {
            $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->dropDatabase();
            $schemaTool->updateSchema($metaData);
        }
    }
}
