<?php

namespace App\Serializer;

use App\Entity\Product;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductNormalizer implements NormalizerInterface
{

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
//        $product = new Product();
//        dump('when');
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return false;
        return $data instanceof Product;
    }
}
