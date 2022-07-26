<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\Entity\DocumentArrayStorage;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\Document;
use Goodwix\DoctrineJsonOdm\Tests\TestCase\FunctionalTestCase;

class DocumentsArrayTest extends FunctionalTestCase
{
    /** @test */
    public function persist_entityWithOdmObject_entityIsSavedWithValidJsonField(): void
    {
        $manager  = $this->getEntityManager();
        $document = $this->givenJsonOdmDocument();
        $document2 = $this->givenSecondJsonOdmDocument();
        $storage  = $this->givenDocumentStorageEntity($document, $document2);

        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        $this->assertTableColumnEqualsToJson(
            DocumentArrayStorage::class,
            'documents',
            '[{"title":"Document title","description":"Document description"}, {"title":"Document title2","description":"Document description2"}]'
        );
    }

    /** @test */
    public function find_entityWithOdmObject_entityRetrievedFromOrmWithValidJsonOdmObject(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithDocumentInDatabase();

        $storage = $manager->find(DocumentArrayStorage::class, $storageId);
        $manager->clear();

        $this->assertValidJsonOdmDocument($storage->documents[0]);
        $this->assertValidSecondJsonOdmDocument($storage->documents[1]);
    }

    /** @test */
    public function find_entityWithNullOdmObject_entityRetrievedFromOrmWithNullValue(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithNullDocumentInDatabase();

        $storage = $manager->find(DocumentArrayStorage::class, $storageId);
        $manager->clear();

        $this->assertNull($storage->documents);
    }

    private function givenJsonOdmDocument(): Document
    {
        $document              = new Document();
        $document->title       = 'Document title';
        $document->description = 'Document description';

        return $document;
    }

    private function givenSecondJsonOdmDocument(): Document
    {
        $document              = new Document();
        $document->title       = 'Document title2';
        $document->description = 'Document description2';

        return $document;
    }

    private function assertValidJsonOdmDocument(Document $document): void
    {
        $this->assertSame('Document title', $document->title);
        $this->assertSame('Document description', $document->description);
    }

    private function assertValidSecondJsonOdmDocument(Document $document): void
    {
        $this->assertSame('Document title2', $document->title);
        $this->assertSame('Document description2', $document->description);
    }

    private function givenDocumentStorageEntity(Document $document, Document $document2): DocumentArrayStorage
    {
        $storage           = new DocumentArrayStorage();
        $storage->documents = [$document, $document2];

        return $storage;
    }

    private function givenDocumentStorageEntityIdWithNullDocumentInDatabase(): int
    {
        $storage = new DocumentArrayStorage();

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }

    private function givenDocumentStorageEntityIdWithDocumentInDatabase(): int
    {
        $document = $this->givenJsonOdmDocument();
        $document2 = $this->givenSecondJsonOdmDocument();
        $storage  = $this->givenDocumentStorageEntity($document, $document2);

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }
}