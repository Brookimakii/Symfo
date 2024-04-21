<?php

	namespace App\Controller;

	use App\Entity\Product;
	use App\Form\ProductType;
	use App\Repository\ProductRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	#[Route('/product')]
	class ProductController extends AbstractController {
		#[Route('/', name: 'app_product_index', methods: ['GET'])]
		public function index(ProductRepository $productRepository): Response {
			return $this->render('product/index.html.twig', [
				'products' => $productRepository->findAll(),
			]);
		}
		#[Route('/category/{id}', name: 'products_in_category', methods: ['GET'])]
		public function prodInCat(ProductRepository $productRepository,$id): Response {
			return $this->render('product/index.html.twig', [
				'products' => $productRepository->findAllByCat($id),
			]);
		}

		#[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
		public function show(Product $product): Response {
			return $this->render('product/show.html.twig', [
				'product' => $product,
			]);
		}
		#[Route('s/search', name: 'app_product_search', methods: ['GET'])]
		public function search(Request $request, ProductRepository $productRepository): Response {
			$query = $request->query->get('q');

			$products = $productRepository->findByKeyword($query);

			return $this->render('product/search.html.twig', [
				'query' => $query,
				'products' => $products,
			]);
		}

	}
