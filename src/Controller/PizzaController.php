<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Form\IngredientsType;
use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PizzaController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function homeAction(PizzaRepository $pizzaRepository): Response
    {
        $pizzas = $pizzaRepository->findAll();

        return $this->render('home.html.twig', [
            'pizzas'    => $pizzas
        ]);
    }

    #[Route('/pizza/{id}', name: 'pizza_detail', requirements: ['id' => '\d+'])]
    public function pizzaDetailAction(Pizza $pizza): Response
    {
        $form = $this->createForm(IngredientsType::class, $pizza);

        return $this->render('pizza_detail.html.twig', [
            'form'  => $form,
            'pizza' => $pizza
        ]);
    }

    #[Route('/get_ingredient_price/{id}', name: 'get_ingredient_price', requirements: ['id' => '\d+'])]
    public function getIngredientPrice(Ingredient $ingredient): JsonResponse
    {
        return new JsonResponse($ingredient->getPrice());
    }
}