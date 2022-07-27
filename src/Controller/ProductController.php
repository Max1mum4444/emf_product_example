<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger)
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
    public function list(ProductRepository $productRepository): Response
    {
        // Example of direct products getting
//        $products = $productRepository->findAllOrdered();

        // Example of getting via api
        $products = $productRepository->getAllProducts();
//        $normalizerProducts = $this->normalizer->normalize($products);
//        dd($products);
//        foreach ($products as $product) {
//            $product->getTitle();
//        }
        //only for now to send it to twig
        $products = $products['hydra:member'];

        return $this->render('product/list.html.twig', ['products' => $products]);
    }
}
