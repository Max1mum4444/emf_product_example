<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Product;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

//class ProductNormalizer implements DenormalizerInterface,DenormalizerAwareInterface
class ProductNormalizer
{
    protected DenormalizerInterface $denormalizer;

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $product = new Product();
        $product->setTitle('myTitle');
        $product->setDescription('myDesc');

        return $product;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $type === Product::class;
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }
}
