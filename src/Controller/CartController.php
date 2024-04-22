<?php

	namespace App\Controller;

	use App\Entity\Cart;
	use App\Entity\CartLine;
	use App\Entity\CommandLine;
	use App\Entity\Order;
	use App\Form\CartType;
	use App\Repository\CartLineRepository;
	use App\Repository\CartRepository;
	use App\Repository\CommandLineRepository;
	use App\Repository\OrderRepository;
	use App\Repository\ProductRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	#[Route('/cart')]
	class CartController extends AbstractController {

		#[Route('/', name: 'app_cart_index', methods: ['GET'])]
		public function index(CartRepository $cartRepository): Response {
			return $this->render('cart/show.html.twig', [
				'cart' => $cartRepository->findCartByUser($this->getUser()),
			]);
		}

		#[Route('/new', name: 'app_cart_new', methods: ['GET', 'POST'])]
		public function new(Request $request, EntityManagerInterface $entityManager): Response {
			$cart = new Cart();
			$form = $this->createForm(CartType::class, $cart);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$entityManager->persist($cart);
				$entityManager->flush();

				return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
			}

			return $this->renderForm('cart/new.html.twig', [
				'cart' => $cart,
				'form' => $form,
			]);
		}

		#[Route('/{id}', name: 'app_cart_show', methods: ['GET'])]
		public function show(Cart $cart): Response {
			return $this->render('cart/show.html.twig', [
				'cart' => $cart,
			]);
		}

		#[Route('/{id}/edit', name: 'app_cart_edit', methods: ['GET', 'POST'])]
		public function edit(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response {
			$form = $this->createForm(CartType::class, $cart);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$entityManager->flush();

				return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
			}

			return $this->renderForm('cart/edit.html.twig', [
				'cart' => $cart,
				'form' => $form,
			]);
		}

		#[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
		public function delete(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response {
			if ($this->isCsrfTokenValid('delete' . $cart->getId(), $request->request->get('_token'))) {
				$entityManager->remove($cart);
				$entityManager->flush();
			}

			return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
		}

		#[Route('/add_to_cart/{id}', name: 'add_to_cart', methods: ['POST'])]
		public function addToCart(Request $request, CartRepository $cartRepository, ProductRepository $productRepository, CartLineRepository $cartLineRepository, EntityManagerInterface $entityManager, $id): Response {
			$user = $this->getUser();
			$product = $productRepository->find($id);
			//Rechercher le panier de l'utilisateur

			$userCart = $cartRepository->findCartByUser($user);

			//Créer le panier utilisateur s'il n'en a pas déjà

			if (!$userCart) {
				$userCart = new Cart();
				$userCart->setUser($user);
				$entityManager->persist($userCart);
				$entityManager->flush();
			}

			//Rechercher le produit dans le panier
			$cartLine = $cartLineRepository->getCartLineByProduct($userCart, $product);

			//Mettre à jour la quantité s'il existe.

			$quantity = $request->request->get('quantity');
			if ($cartLine) {
				$newQuantity = $cartLine->getQuantity() + $quantity;
				$cartLine->setQuantity($newQuantity);
			} else {
				$cartLine = new CartLine();
				$cartLine->setCart($userCart);
				$cartLine->setProduct($product);
				$cartLine->setQuantity($quantity);
				$entityManager->persist($cartLine);
			}
			$entityManager->flush();

			//Créer le CartLine pour le produit s'il n'en a pas déjà
			return $this->redirect($request->headers->get('referer'));
		}

		#[Route('/checkout/', name: 'app_checkout', methods: ['POST'])]
		public function checkout(Request $request, CartRepository $cartRepository, OrderRepository $orderRepository, CommandLineRepository $commandLineRepository, EntityManagerInterface $entityManager): Response {
			$user = $this->getUser();
			$cart = $cartRepository->findCartByUser($user);
			$cartLines = $cart->getCartLine();

			$order = $orderRepository->findByCart($cart);
			if (!$order) {
				$order = new Order();
				$order->setUser($user);
				$order->setCart($cart);
				$order->setValid(false);
				$order->setDateTime(new \DateTime());
				foreach ($cartLines as $cartLine) {
					$commandLines = new CommandLine();
					$commandLines->setQuantity($cartLine->getQuantity());
					$commandLines->setProduct($cartLine->getProduct());
					$order->addCommandLine($commandLines);
					$entityManager->persist($cartLine);
				}
				$entityManager->persist($order);
			}else{
				foreach ($cartLines as $cartLine){
					$commandLine = $order->getCommandLine();

					$commandLine->

					$order->updateCommandLine($cartLine);
					$existingOrderLine = $this->getOrderLineByProduct($cartLine->getProduct());
					if ($existingOrderLine) {
						// Si une ligne de commande existe déjà, mettez à jour sa quantité
						$existingOrderLine->setQuantity($existingOrderLine->getQuantity() + $cartLine->getQuantity());
					} else {
						$orderLine = new CommandLine();
						$orderLine->setQuantity($cartLine->getQuantity());
					}
					foreach ($this->getCommandLine() as $commandLine) {
						if ($commandLine->getProduct() === $product) {
							return $commandLine;
						}
					}
				}
			}

			$entityManager->flush();
			return $this->redirect($request->headers->get('referer'));
		}
	}
