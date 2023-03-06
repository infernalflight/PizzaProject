<?php

namespace App\Controller;

use App\Entity\Pizza;
use App\Form\PizzaType;
use App\Repository\PizzaRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/pizza', name: 'admin_pizza_')]
class AdminPizzaController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function listAction(PizzaRepository $pizzaRepository): Response
    {
        $pizzas = $pizzaRepository->findBy([], ['id' => 'ASC']);

        return $this->render('admin/pizzas.html.twig', [
            'pizzas' => $pizzas
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '\d+'])]
    public function editAction(
        Request $request,
        PizzaRepository $pizzaRepository,
        SluggerInterface $slugger,
        Pizza $pizza = null
    ): Response
    {
        if (!$pizza) {
            $pizza = new Pizza();
        }

        $form = $this->createForm(PizzaType::class, $pizza);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $pizza = $form->getData();

                $file = $form->get('file')->getData();

                if ($file) {
                    $safeFilename = strtolower($slugger->slug($pizza->getName()));
                    $newFilename = $safeFilename.'.'.$file->guessExtension();

                    try {
                        $file->move(
                            $this->getParameter('uploads'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        throw new FileException($e->getMessage());
                    }

                    $pizza->setPicture($newFilename);
                }

                $pizzaRepository->save($pizza, true);

                $this->addFlash('success', 'Pizza édité');
                return $this->redirectToRoute('admin_pizza_list');
            }
        }

        return $this->render('admin/edit_pizza.html.twig', [
            'form' => $form,
            'pizza' => $pizza
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function deleteAction(PizzaRepository $pizzaRepository, Pizza $pizza): Response
    {
        try {
            $pizzaRepository->remove($pizza, true);
            $this->addFlash('success', 'Pizza supprimée');
        } catch (\Exception $e) {
            $this->addFlash('warning', 'Erreur à la suppression: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_pizza_list');
    }
}