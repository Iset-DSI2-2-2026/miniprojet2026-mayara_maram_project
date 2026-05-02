<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivreController extends AbstractController
{
    // 📚 LISTE DES LIVRES
  #[Route('/livre', name: 'app_livres')]
public function index(LivreRepository $livreRepository): Response
{
    $livres = $livreRepository->findAll();

    return $this->render('livre/index.html.twig', [
        'livres' => $livres,
    ]);
}

    
    // 👁️ DÉTAIL LIVRE
     #[Route('/livres/{id}', name: 'app_livre_detail', requirements: ['id' => '\d+'])]
    public function detail(Livre $livre): Response
    {
        return $this->render('livre/detail.html.twig', [
            'livre' => $livre,
        ]);
    }


       // ➕ AJOUT LIVRE
    #[Route('/livres/nouveau', name: 'app_livre_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $livre = new Livre();

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté avec succès !');

            return $this->redirectToRoute('app_livres');
        }

        return $this->render('livre/nouveau.html.twig', [
            'formulaire' => $form,
        ]);
    }



    // ✏️ MODIFIER LIVRE
    #[Route('/livres/{id}/modifier', name: 'app_livre_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Livre $livre, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Livre modifié avec succès !');

            return $this->redirectToRoute('app_livre_detail', [
                'id' => $livre->getId()
            ]);
        }

        return $this->render('livre/modifier.html.twig', [
            'formulaire' => $form,
            'livre' => $livre,
        ]);
    }

    // 🗑️ SUPPRIMER LIVRE (CSRF)
    #[Route('/livres/{id}/supprimer', name: 'app_livre_supprimer', methods: ['POST'])]
    public function supprimer(Livre $livre, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('supprimer_'.$livre->getId(), $request->request->get('_token'))) {

            $em->remove($livre);
            $em->flush();

            $this->addFlash('success', 'Livre supprimé avec succès !');

        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_livres');
    }
}

    

