# Doctrine JSON ODM library

[![Latest Stable Version](https://poser.pugx.org/goodwix/doctrine-json-odm/v/stable)](https://packagist.org/packages/goodwix/doctrine-json-odm)
[![Total Downloads](https://poser.pugx.org/goodwix/doctrine-json-odm/downloads)](https://packagist.org/packages/goodwix/doctrine-json-odm)
[![License](https://poser.pugx.org/goodwix/doctrine-json-odm/license)](https://packagist.org/packages/goodwix/doctrine-json-odm)
[![Build Status](https://scrutinizer-ci.com/g/goodwix/doctrine-json-odm/badges/build.png?b=master)](https://scrutinizer-ci.com/g/goodwix/doctrine-json-odm/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/goodwix/doctrine-json-odm/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/goodwix/doctrine-json-odm/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/goodwix/doctrine-json-odm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/goodwix/doctrine-json-odm/?branch=master)

Inspired by <https://github.com/dunglas/doctrine-json-odm>

This is beta version of library. Main differences from Dunglas library:

* library does not store any metadata in json field;
* doctrine ODM type uses main Symfony serializer service (so you can easily extend serialization process by adding normalizers/denormalizers globally in dependency injection config);
* automatic registration of ODM types for Doctrine (using Symfony autowiring and autoconfigure features).

## Features

* Object-Document Mapping with database json types
* Doctrine 2.5+ support
* PostgreSQL 9.4+ support
* Symfony 4+ support (not tested with Symfony 2 and 3)
* MySQL support not tested

## Additional features

* Automatic registering normalizers for use with Java-like collections from [ramsey/collection](https://github.com/ramsey/collection) library

## Install

### Install with Symfony 4

To install the library, use [Composer](https://getcomposer.org/).

```bash
composer require goodwix/doctrine-json-odm
```

Add lines to `config/bundles.php` (no automatic configuration is available for beta version).

```php
<?php

return [
    // ...
    Goodwix\DoctrineJsonOdm\Bridge\Symfony\DoctrineJsonOdmBundle::class => ['all' => true],
];

```

Create package config file `config/packages/doctrine-json-odm.yaml` with next content.

```yaml
doctrine_json_odm:
  mapping:
    paths:
      - '%kernel.project_dir%/src/ODM'
```

there `src/ODM` is the root path for your ODM entities (like `src/Entity` for Doctrine).

## Usage

### Basic usage with automatic ODM types registration

Create entity class for ODM type in ODM-specific directory (like `src/ODM`) and mark it with `\Goodwix\DoctrineJsonOdm\Annotation\ODM` annotation.

```php
namespace App\ODM;

use Goodwix\DoctrineJsonOdm\Annotation\ODM;

/**
 * @ODM()
 */
class Document
{
    /** @var string */
    public $title;

    /** @var string */
    public $description;
}
```

Create doctrine entity class with field type `App\ODM\Document`.

```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ODM\Document;

/**
 * @ORM\Entity()
 */
class DocumentStorage
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(type=Document::class, nullable=true)
     *
     * @var Document
     */
    public $document;
}
```

Now you can easily manage your ORM entity with ODM class field

```php
$documentStorage = $entityManager->find(DocumentStorage::class, $id);
$documentStorage->document->title = 'ODM document title';
$documentStorage->document->description = 'ODM document description';
```

### Manual usage with Doctrine and Symfony Serializer component

To manually register Doctrine ODM types use [`ODMType::registerODMType()`](https://github.com/goodwix/doctrine-json-odm/blob/36860ddaddc10e9ea33b2986b17009db979a0026/src/Type/ODMType.php#L100) method.

```php
require_once __DIR__.'/../vendor/autoload.php';

use Goodwix\DoctrineJsonOdm\Type\ODMType;
use Symfony\Component\Serializer\SerializerInterface;

class Document { }

ODMType::registerODMType(
    Document::class,
    new class implements SerializerInterface
    {
        public function serialize($data, $format, array $context = [])  { /* Implement serialize() method. */ }
        public function deserialize($data, $type, $format, array $context = [])  { /* Implement deserialize() method. */ }
    }
);
```

### Examples with Symfony application

You can see example of Symfony 4 application with using ODM library in this [directory](https://github.com/goodwix/doctrine-json-odm/tree/master/tests/Resources/Symfony).
