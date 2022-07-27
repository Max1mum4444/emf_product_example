<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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
    ];

    public function __construct(
        ManagerRegistry $registry,
        protected LoggerInterface $logger,
        protected string $apiUrl,
        protected CacheInterface $cache,
        protected int $cacheTtl
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
    public function getAllProducts(): array
    {
        //this should be in Repository of 2nd app
        $cacheTtl = $this->cacheTtl;

        try {
            return $this->cache->get('all_products', function (ItemInterface $item) use ($cacheTtl) {
                $item->expiresAfter($cacheTtl);
                $client = HttpClient::create();

                return $client->request('GET', $this->apiUrl.static::ENDPOINT['list'])->toArray();
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
