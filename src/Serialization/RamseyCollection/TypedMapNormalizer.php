<?php
/*
 * This file is part of Goodwix Doctrine JSON ODM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goodwix\DoctrineJsonOdm\Serialization\RamseyCollection;

use Ramsey\Collection\Map\MapInterface;
use Ramsey\Collection\Map\TypedMapInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class TypedMapNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        $supports = false;

        if (class_exists($type)) {
            $reflectionClass = new \ReflectionClass($type);
            $inheritsClass   = $reflectionClass->isSubclassOf(MapInterface::class);
            $supports        = is_array($data) && $inheritsClass;
        }

        return $supports;
    }

    public function denormalize($data, $class, $format = null, array $context = []): TypedMapInterface
    {
        /** @var TypedMapInterface $map */
        $map = new $class();

        $itemType = $map->getValueType();

        if (class_exists($itemType)) {
            foreach ($data as $key => $item) {
                $item = $this->denormalizer->denormalize($item, $itemType, $format, $context);
                $map->put($key, $item);
            }
        } else {
            foreach ($data as $key => $item) {
                $map->put($key, $item);
            }
        }

        return $map;
    }
}
