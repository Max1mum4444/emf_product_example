<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Serializer\ProductNormalizer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly NormalizerInterface $normalizer)
    {
    }

    #[Route('/product', name: 'app_product')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProductController.php',
        ]);
    }

    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(int $id, ProductRepository $productRepository): Response
    {
        return $this->render('product/');
    }

    #[Route('/products', name: 'product_list')]
    public function list(ProductRepository $productRepository, HttpClientInterface $client): Response
    {
        // Example of direct products getting
//        $products = $productRepository->findAllOrdered();

        $products = [];
        try {
            $response = $client->request('GET', 'http://nginx-container/api/products');
            $products = $response->toArray();
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }
        $normalizerProducts = $this->normalizer->normalize($products);
//        dd($products);
//        foreach ($products as $product) {
//            $product->getTitle();
//        }


        return $this->render('product/list.html.twig', ['products' => $products]);
    }
}
