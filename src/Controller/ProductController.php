<?php
declare(strict_types=1);

namespace App\Controller;

use App\Elastic\ProductFinder;
use App\Repository\ProductRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductFinder $productFinder,
        private readonly ProductRepository $productRepository
    )
    {
    }

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $products = $this->getProducts(1);

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/products/{productId}', name: 'product_detail')]
    public function detail(int $productId): Response
    {
        $product = $this->productRepository->getProductById($productId);

        return $this->render('product/detail.html.twig', ['product' => $product]);
    }

    #[Route('/products', name: 'product_list')]
    public function list(Request $request): Response
    {
        // EXAMPLE of direct products getting
//        $products = $this->productRepository->findAllOrdered();

        // EXAMPLE of getting via api
//        $products = $this->productRepository->getAllProducts();
        //only for now to send it to twig
//        $products = $products['hydra:member'];

        $search = $request->query->get('search');
        $page = $request->query->get('page', 1);
        $products = $this->getProducts($page, $search);

        //search by string

        return $this->render('product/list.html.twig', ['products' => $products]);
    }

    private function getProducts(int $page, ?string $search = null): Pagerfanta
    {
        if (null !== $search) {
            return $this->productFinder->searchProductsByText($search, $page);
        }

        return $this->productRepository->getAllProducts($page);
    }
}
