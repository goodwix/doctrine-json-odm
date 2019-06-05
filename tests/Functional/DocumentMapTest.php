<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\Entity\DocumentMapStorage;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\Document;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\DocumentMap;
use Goodwix\DoctrineJsonOdm\Tests\TestCase\FunctionalTestCase;

class DocumentMapTest extends FunctionalTestCase
{
    /** @test */
    public function persist_entityWithOdmObjectsMap_entityIsSavedWithValidJsonField(): void
    {
        $manager = $this->getEntityManager();
        $storage = $this->givenDocumentMapStorage();

        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        $this->assertTableColumnEqualsToJson(
            DocumentMapStorage::class,
            'documents',
            '{
                "key": {
                    "title": "Document title",
                    "description": "Document description"
                }
            }'
        );
    }

    /** @test */
    public function find_entityWithOdmObjectsMap_entityRetrievedFromOrmWithValidJsonOdmObjects(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithDocumentInDatabase();

        /** @var DocumentMapStorage $storage */
        $storage = $manager->find(DocumentMapStorage::class, $storageId);
        $manager->clear();

        $this->assertValidJsonOdmDocumentMap($storage->documents);
    }

    private function givenDocumentStorageEntityIdWithDocumentInDatabase(): int
    {
        $storage = $this->givenDocumentMapStorage();

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }

    private function givenDocumentMapStorage(): DocumentMapStorage
    {
        $document              = new Document();
        $document->title       = 'Document title';
        $document->description = 'Document description';

        $storage = new DocumentMapStorage();
        $storage->documents->put('key', $document);

        return $storage;
    }

    private function assertValidJsonOdmDocumentMap(DocumentMap $documents): void
    {
        $this->assertCount(1, $documents);
        $this->assertArrayHasKey('key', $documents);

        /** @var Document $document */
        $document = $documents->get('key');
        $this->assertSame('Document title', $document->title);
        $this->assertSame('Document description', $document->description);
    }
}
