<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Tests\Functional;

use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\Entity\EmbeddedDocumentStorage;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\Document;
use Goodwix\DoctrineJsonOdm\Tests\Resources\Symfony\TestBundle\ODM\DocumentHolder;
use Goodwix\DoctrineJsonOdm\Tests\TestCase\FunctionalTestCase;

class EmbeddedDocumentTest extends FunctionalTestCase
{
    /** @test */
    public function persist_entityWithEmbeddedOdmObjects_entityIsSavedWithValidJsonField(): void
    {
        $manager = $this->getEntityManager();
        $storage = $this->givenEmbeddedDocumentStorageWithEmbeddedJsonOdmObjects();

        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        $this->assertTableColumnEqualsToJson(
            EmbeddedDocumentStorage::class,
            'documentHolder',
            '{
                "mainDocument": {
                    "title": "Main document title",
                    "description": "Main document description"
                },
                "documents": [
                    {
                        "title": "Embedded document title 1",
                        "description": "Embedded document description 1"
                    },
                    {
                        "title": "Embedded document title 2",
                        "description": "Embedded document description 2"
                    }
                ]
            }'
        );
    }

    /** @test */
    public function find_entityWithEmbeddedOdmObjects_entityRetrievedFromOrmWithValidJsonOdmObjects(): void
    {
        $manager   = $this->getEntityManager();
        $storageId = $this->givenDocumentStorageEntityIdWithDocumentInDatabase();

        $storage = $manager->find(EmbeddedDocumentStorage::class, $storageId);
        $manager->clear();

        $this->assertValidJsonOdmDocumentHolder($storage->documentHolder);
    }

    private function givenDocumentStorageEntityIdWithDocumentInDatabase(): int
    {
        $storage = $this->givenEmbeddedDocumentStorageWithEmbeddedJsonOdmObjects();

        $manager = $this->getEntityManager();
        $manager->persist($storage);
        $manager->flush();
        $manager->clear();

        return $storage->id;
    }

    private function givenEmbeddedDocumentStorageWithEmbeddedJsonOdmObjects(): EmbeddedDocumentStorage
    {
        $mainDocument              = new Document();
        $mainDocument->title       = 'Main document title';
        $mainDocument->description = 'Main document description';

        $document1              = new Document();
        $document1->title       = 'Embedded document title 1';
        $document1->description = 'Embedded document description 1';

        $document2              = new Document();
        $document2->title       = 'Embedded document title 2';
        $document2->description = 'Embedded document description 2';

        $holder               = new DocumentHolder();
        $holder->mainDocument = $mainDocument;
        $holder->documents    = [
            $document1,
            $document2,
        ];

        $storage                 = new EmbeddedDocumentStorage();
        $storage->documentHolder = $holder;

        return $storage;
    }

    private function assertValidJsonOdmDocumentHolder(DocumentHolder $holder): void
    {
        $this->assertSame('Main document title', $holder->mainDocument->title);
        $this->assertSame('Main document description', $holder->mainDocument->description);
        $this->assertCount(2, $holder->documents);
        $this->assertSame('Embedded document title 1', $holder->documents[0]->title);
        $this->assertSame('Embedded document description 1', $holder->documents[0]->description);
        $this->assertSame('Embedded document title 2', $holder->documents[1]->title);
        $this->assertSame('Embedded document description 2', $holder->documents[1]->description);
    }
}
