<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public const CACHE_LIFETIME = 3600;
    private const ENDPOINT = [
        'list' => '/api/products',
        'detail' => '/api/products/%s',
    ];

    public function __construct(
        ManagerRegistry $registry,
        private readonly LoggerInterface $logger,
        private readonly string $apiUrl,
        private readonly CacheInterface $cache,
        private readonly int $cacheTtl,
        private readonly int $apiItemsPerPage
//        protected SerializerInterface $serializer
    )
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->enableResultCache(static::CACHE_LIFETIME)
            ->getResult();
    }

    // this should be moved in other app to process output from this app via client and api
    public function getAllProducts($page = 1): Pagerfanta
    {
        //this should be in Repository of 2nd app
        $cacheTtl = $this->cacheTtl;

        try {
            return $this->cache->get('all_products', function (ItemInterface $item) use ($cacheTtl, $page) {
                $item->expiresAfter($cacheTtl);
                $client = HttpClient::create();
                $url = sprintf('%s%s%s', $this->apiUrl, static::ENDPOINT['list'], $page > 1 ? sprintf('?page=%s', $page) : '');
                $response = $client->request('GET', $url);
                $products = $response->toArray();
                $adapter = new ArrayAdapter($products['hydra:member']);
                $pagerfanta = new Pagerfanta($adapter);
                $pagerfanta->setMaxPerPage($this->apiItemsPerPage);
                $pagerfanta->setCurrentPage($page); // 1 by default

                return $pagerfanta;
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());

            return new Pagerfanta(new ArrayAdapter([]));
        }
    }

    // this should be moved in other app to process output from this app via client and api
    public function getProductById(int $id): array
    {
        //this should be in Repository of 2nd app
        $cacheTtl = $this->cacheTtl;
        try {
            $key = sprintf('product_%s', $id);

            return $this->cache->get($key, function (ItemInterface $item) use ($cacheTtl, $id) {
                $item->expiresAfter($cacheTtl);
                $client = HttpClient::create();

                $url = sprintf('%s%s', $this->apiUrl, sprintf(static::ENDPOINT['detail'], $id));

                return $client->request('GET', $url)->toArray();
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());

            return [];
        }
    }
}
