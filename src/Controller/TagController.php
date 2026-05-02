<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TagController extends AbstractController
{
   // 📚 LISTE DES TAGS
    #[Route('/tag', name: 'app_tag')]
    public function index(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAll();

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    // ➕ AJOUT TAG (avec TagType)
    #[Route('/tag/nouveau', name: 'app_tag_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', 'Tag ajouté avec succès !');

            return $this->redirectToRoute('app_tag');
        }

        return $this->render('tag/nouveau.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

    // 🗑️ SUPPRESSION TAG (CSRF)
    #[Route('/tag/{id}/supprimer', name: 'app_tag_supprimer', methods: ['POST'])]
    public function supprimer(Tag $tag, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {

            $em->remove($tag);
            $em->flush();

            $this->addFlash('success', 'Tag supprimé avec succès !');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_tag_index');
    }


    }
