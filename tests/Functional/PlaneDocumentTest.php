<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\Entity\DocumentStorage;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\Document;
use Goodwix\DoctrineJsonOdm\Tests\TestCase\FunctionalTestCase;

class PlaneDocumentTest extends FunctionalTestCase
{
    /** @test */
    public function persist_entityWithOdmObject_entityIsSavedWithValidJsonField(): void
    {
        $manager  = $this->getEntityManager();
        $document = $this->givenJsonOdmDocument();
        $storage  = $this->givenDocumentStorageEntity($document);

        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        $this->assertTableColumnEqualsToJson(
            DocumentStorage::class,
            'document',
            '{"title":"Document title","description":"Document description"}'
        );
    }

    /** @test */
    public function find_entityWithOdmObject_entityRetrievedFromOrmWithValidJsonOdmObject(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithDocumentInDatabase();

        $storage = $manager->find(DocumentStorage::class, $storageId);
        $manager->clear();

        $this->assertValidJsonOdmDocument($storage->document);
    }

    /** @test */
    public function find_entityWithNullOdmObject_entityRetrievedFromOrmWithNullValue(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithNullDocumentInDatabase();

        $storage = $manager->find(DocumentStorage::class, $storageId);
        $manager->clear();

        $this->assertNull($storage->document);
    }

    private function givenJsonOdmDocument(): Document
    {
        $document              = new Document();
        $document->title       = 'Document title';
        $document->description = 'Document description';

        return $document;
    }

    private function assertValidJsonOdmDocument(Document $document): void
    {
        $this->assertSame('Document title', $document->title);
        $this->assertSame('Document description', $document->description);
    }

    private function givenDocumentStorageEntity(Document $document): DocumentStorage
    {
        $storage           = new DocumentStorage();
        $storage->document = $document;

        return $storage;
    }

    private function givenDocumentStorageEntityIdWithNullDocumentInDatabase(): int
    {
        $storage = new DocumentStorage();

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }

    private function givenDocumentStorageEntityIdWithDocumentInDatabase(): int
    {
        $document = $this->givenJsonOdmDocument();
        $storage  = $this->givenDocumentStorageEntity($document);

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }
}
