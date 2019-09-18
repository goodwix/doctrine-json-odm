<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\Entity\ContextualDocumentStorage;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\ContextualDocument;
use Goodwix\DoctrineJsonOdm\Tests\TestCase\FunctionalTestCase;

class ContextualDocumentTest extends FunctionalTestCase
{
    /** @test */
    public function persist_entityWithOdmObjectAndContext_entityIsSavedWithValidJsonField(): void
    {
        $manager  = $this->getEntityManager();
        $document = $this->givenJsonOdmDocument();
        $storage  = $this->givenContextualDocumentStorageEntity($document);

        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        $this->assertTableColumnEqualsToJson(
            ContextualDocumentStorage::class,
            'document',
            '{"title":"Document title"}'
        );
    }

    /** @test */
    public function find_entityWithOdmObject_entityRetrievedFromOrmWithValidJsonOdmObject(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithDocumentInDatabase();

        $storage = $manager->find(ContextualDocumentStorage::class, $storageId);
        $manager->clear();

        $this->assertValidJsonOdmDocument($storage->document);
    }

    private function givenJsonOdmDocument(): ContextualDocument
    {
        return new ContextualDocument('Document title', 'Read only');
    }

    private function assertValidJsonOdmDocument(ContextualDocument $document): void
    {
        $this->assertSame('Document title', $document->title);
    }

    private function givenContextualDocumentStorageEntity(ContextualDocument $document): ContextualDocumentStorage
    {
        $storage           = new ContextualDocumentStorage();
        $storage->document = $document;

        return $storage;
    }

    private function givenDocumentStorageEntityIdWithDocumentInDatabase(): int
    {
        $document = $this->givenJsonOdmDocument();
        $storage  = $this->givenContextualDocumentStorageEntity($document);

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }
}
