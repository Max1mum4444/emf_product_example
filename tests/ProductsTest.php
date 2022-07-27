<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ProductsTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/products');

        static::assertResponseIsSuccessful();
        static::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        static::assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/products?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/products?page=1',
                'hydra:last' => '/api/products?page=4',
                'hydra:next' => '/api/products?page=2',
            ],
        ]);
        $this->assertCount(25, $response->toArray()['hydra:member']);
    }

    public function testPagination(): void
    {
        $response = static::createClient()->request('GET', '/api/products?page=2');

        static::assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/products?page=2',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/products?page=1',
                'hydra:last' => '/api/products?page=4',
                'hydra:previous' => '/api/products?page=1',
                'hydra:next' => '/api/products?page=3',
            ],
        ]);
    }

    public function testCreateProduct(): void
    {
        static::createClient()->request('POST', '/api/products', [
            'json' => [
                'title' => 'iPhone 11 Pro Max',
                'description' => 'We pushed it even further',
                'price' => '999',
                'category' => '/api/categories/1',
            ],
        ]);
        static::assertResponseStatusCodeSame(201);

        static::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        static::assertJsonContains([
            'title' => 'iPhone 11 Pro Max',
            'description' => 'We pushed it even further',
            'price' => '999.00',
        ]);
    }
    public function testUpdateProduct(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/api/products/1', [
            'json' => [
                'description' => 'We pushed it even further. More powerful than ever',
            ],
        ]);

        static::assertResponseIsSuccessful();
        static::assertJsonContains([
            '@id' => '/api/products/1',
            'description' => 'We pushed it even further. More powerful than ever',
        ]);
    }
}
