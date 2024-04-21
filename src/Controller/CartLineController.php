<?php

	namespace App\Controller;

	use App\Entity\CartLine;
	use App\Entity\Order;
	use App\Form\CartLineType;
	use App\Repository\CartLineRepository;
	use App\Repository\OrderRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	#[Route('/cart/line')]
	class CartLineController extends AbstractController {
		#[Route('/', name: 'app_cart_line_index', methods: ['GET'])]
		public function index(CartLineRepository $cartLineRepository): Response {
			return $this->render('cart_line/index.html.twig', [
				'cart_lines' => $cartLineRepository->findAll(),
			]);
		}

		#[Route('/new', name: 'app_cart_line_new', methods: ['GET', 'POST'])]
		public function new(Request $request, EntityManagerInterface $entityManager): Response {
			$cartLine = new CartLine();

			if ($form->isSubmitted() && $form->isValid()) {
				$entityManager->persist($cartLine);
				$entityManager->flush();

				return $this->redirectToRoute('app_cart_line_index', [], Response::HTTP_SEE_OTHER);
			}

			return $this->renderForm('cart_line/new.html.twig', [
				'cart_line' => $cartLine,
				'form' => $form,
			]);
		}

		#[Route('/{id}', name: 'app_cart_line_show', methods: ['GET'])]
		public function show(CartLine $cartLine): Response {
			return $this->render('cart_line/show.html.twig', [
				'cart_line' => $cartLine,
			]);
		}

		#[Route('/{id}/edit', name: 'app_cart_line_edit', methods: ['GET', 'POST'])]
		public function edit(Request $request, CartLine $cartLine, EntityManagerInterface $entityManager): Response {
			$form = $this->createForm(CartLineType::class, $cartLine);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$entityManager->flush();

				return $this->redirectToRoute('app_cart_line_index', [], Response::HTTP_SEE_OTHER);
			}

			return $this->renderForm('cart_line/edit.html.twig', [
				'cart_line' => $cartLine,
				'form' => $form,
			]);
		}

		#[Route('/addOne/{id}', name: 'app_cart_line_add', methods: ['POST'])]
		public function add(Request $request, CartLineRepository $cartLineRepository, EntityManagerInterface $entityManager, $id): Response {
			$cartLine = $cartLineRepository->find($id);
			echo "Add";

			if ($cartLine) {
				$cartLine->setQuantity($cartLine->getQuantity() + 1);
				$entityManager->flush();
			}

			return $this->redirect($request->headers->get('referer'));
		}


		#[Route('/removeOne/{id}', name: 'app_cart_line_remove', methods: ['POST'])]
		public function remove(Request $request, CartLineRepository $cartLineRepository, EntityManagerInterface $entityManager, $id): Response {
			$cartLine = $cartLineRepository->find($id);
			echo "Minus";
			if ($cartLine) {
				$cartLine->setQuantity($cartLine->getQuantity() - 1);
				$entityManager->flush();
			}

			return $this->redirect($request->headers->get('referer'));
		}

		#[Route('/{id}', name: 'app_cart_line_delete', methods: ['POST'])]
		public function delete(Request $request, CartLine $cartLine, EntityManagerInterface $entityManager): Response {
			if ($this->isCsrfTokenValid('delete' . $cartLine->getId(), $request->request->get('_token'))) {
				$entityManager->remove($cartLine);
				$entityManager->flush();
			}

			return $this->redirect($request->headers->get('referer'));
		}

	}
