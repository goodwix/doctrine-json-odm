<?php
declare(strict_types=1);

namespace Goodwix\DoctrineJsonOdm;

interface EntityWithOdmInterface
{
    /** @return string[] */
    public function getOdmFieldList(): array;
}
