<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ingredient', name: 'admin_ingredient_')]
class AdminIngredientController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function listAction(IngredientRepository $ingredientRepository): Response
    {
        $ingredients = $ingredientRepository->findBy([], ['id' => 'ASC']);

        return $this->render('admin/ingredients.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '\d+'])]
    public function editAction(
        Request $request,
        IngredientRepository $ingredientRepository,
        Ingredient $ingredient = null
    ): Response
    {
        if (!$ingredient) {
            $ingredient = new Ingredient();
        }

        $form = $this->createForm(IngredientType::class, $ingredient);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $ingredient = $form->getData();
                $ingredientRepository->save($ingredient, true);

                $this->addFlash('success', 'Ingrédient édité');
                return $this->redirectToRoute('admin_ingredient_list');
            }
        }

        return $this->render('admin/edit_ingredient.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function deleteAction(IngredientRepository $ingredientRepository, Ingredient $ingredient): Response
    {
        try {
            $ingredientRepository->remove($ingredient, true);
            $this->addFlash('success', 'Ingredient supprimé');
        } catch (\Exception $e) {
            $this->addFlash('warning', 'Erreur à la suppression: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_ingredient_list');
    }
}
