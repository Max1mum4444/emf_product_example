<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
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
        //TODO pagination
        $products = $productRepository->findAllOrdered();
        return $this->render('product/list.html.twig', ['products' => $products]);
    }
}
