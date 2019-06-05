<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\Entity\DocumentCollectionStorage;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\Document;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\DocumentCollection;
use Goodwix\DoctrineJsonOdm\Tests\TestCase\FunctionalTestCase;

class DocumentCollectionTest extends FunctionalTestCase
{
    /** @test */
    public function persist_entityWithOdmCollectionObjects_entityIsSavedWithValidJsonField(): void
    {
        $manager = $this->getEntityManager();
        $storage = $this->givenDocumentCollectionStorage();

        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        $this->assertTableColumnEqualsToJson(
            DocumentCollectionStorage::class,
            'documents',
            '[
                {
                    "title": "Document title",
                    "description": "Document description"
                }
            ]'
        );
    }

    /** @test */
    public function find_entityWithOdmCollectionObjects_entityRetrievedFromOrmWithValidJsonOdmObjects(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithDocumentInDatabase();

        /** @var DocumentCollectionStorage $storage */
        $storage = $manager->find(DocumentCollectionStorage::class, $storageId);
        $manager->clear();

        $this->assertValidJsonOdmDocumentCollection($storage->documents);
    }

    private function givenDocumentStorageEntityIdWithDocumentInDatabase(): int
    {
        $storage = $this->givenDocumentCollectionStorage();

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }

    private function givenDocumentCollectionStorage(): DocumentCollectionStorage
    {
        $document              = new Document();
        $document->title       = 'Document title';
        $document->description = 'Document description';

        $storage = new DocumentCollectionStorage();
        $storage->documents->add($document);

        return $storage;
    }

    private function assertValidJsonOdmDocumentCollection(DocumentCollection $documents): void
    {
        $this->assertCount(1, $documents);

        /** @var Document $document */
        $document = $documents->first();
        $this->assertSame('Document title', $document->title);
        $this->assertSame('Document description', $document->description);
    }
}
