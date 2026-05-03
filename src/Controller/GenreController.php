<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class GenreController extends AbstractController
{
    #[Route('/genres', name: 'app_genres')]
    public function index(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findAll();

        return $this->render('genre/index.html.twig', [
            'genres' => $genres,
        ]);
    }

    #[Route('/genres/nouveau', name: 'app_genre_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $genre = new Genre();

        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($genre);
            $em->flush();

            $this->addFlash('success', 'Genre ajouté avec succès !');

            return $this->redirectToRoute('app_genres');
        }

        return $this->render('genre/nouveau.html.twig', [
            'formulaire' => $form,
        ]);
    }








}
