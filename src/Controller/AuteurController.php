<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AuteurController extends AbstractController
{
  

 #[Route('/auteurs', name: 'app_auteurs')]
    public function index(AuteurRepository $auteurRepository,PaginatorInterface $paginator,Request $request): Response
    {
        // $auteurs = $auteurRepository->findAll();

        // QueryBuilder
        $query = $auteurRepository->createQueryBuilder('a')
            ->orderBy('a.nom', 'ASC');

        // Pagination
        $auteurs = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );


        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurs,
        ]);
    }



    
    #[Route('/auteurs/nouveau', name: 'app_auteur_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $auteur = new Auteur();

        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($auteur);
            $em->flush();

            $this->addFlash('success', 'Auteur ajouté avec succès !');

            return $this->redirectToRoute('app_auteurs');
        }

        return $this->render('auteur/nouveau.html.twig', [
            'formulaire' => $form,
        ]);
    }
}





