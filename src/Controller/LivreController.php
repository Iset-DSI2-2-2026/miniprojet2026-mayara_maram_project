<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\GenreRepository;
use App\Repository\LivreRepository;
use App\Repository\TagRepository;
use App\Service\BibliothequeStats;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\mailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class LivreController extends AbstractController
{


    private BibliothequeStats $stats;

    // ✅ Injection par constructeur (version classique)
    public function __construct(BibliothequeStats $stats)
    {
        $this->stats = $stats;
    }






    // 📚 LISTE DES LIVRES
//   #[Route('/livre', name: 'app_livres')]
// public function index(LivreRepository $livreRepository): Response
// {
//     $livres = $livreRepository->findAll();

//     return $this->render('livre/index.html.twig', [
//         'livres' => $livres,
//     ]);
// }


#[Route('/livre', name: 'app_livres')]
public function index(
    Request $request,
    LivreRepository $livreRepository,
    GenreRepository $genreRepository,
    TagRepository $tagRepository,
    PaginatorInterface $paginator

): Response {

    // 🔍 récupérer filtres
    $titre = $request->query->get('titre');
    $genreId = $request->query->get('genre');
    $disponible = $request->query->get('disponible');
    $tagId = $request->query->get('tag');

    // 📚 convertir ID → objet
    $genre = $genreId ? $genreRepository->find($genreId) : null;
    $tag = $tagId ? $tagRepository->find($tagId) : null;

    // ✅ convertir disponible
    if ($disponible !== null && $disponible !== '') {
        $disponible = (bool) $disponible;
    } else {
        $disponible = null;
    }

    // 🔥 appel repository
    // $livres = $livreRepository->findByFilters(
    //     $titre,
    //     $genre,
    //     $disponible,
    //     $tag
    // );

    // 🔥 appel repository
$query = $livreRepository->findByFilters(
    $titre,
    $genre,
    $disponible,
    $tag
);
    // 📄 pagination
        $livres = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

    
    return $this->render('livre/index.html.twig', [
        'livres' => $livres,
        'genres' => $genreRepository->findAll(), // ✅ AJOUT
        'tags' => $tagRepository->findAll(),     // ✅ AJOUT
         'totalLivres' => $this->stats->getTotalLivres(),
            'livresDispo' => $this->stats->getLivresDisponibles(),
            'livresParGenre' => $this->stats->getLivresParGenre(),
            'tempsLecture' => $this->stats->getTempsLectureTotal(),
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
     #[IsGranted('ROLE_BIBLIOTHECAIRE')]

    #[Route('/livres/nouveau', name: 'app_livre_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em, MailerInterface $mailer, FileUploader $fileUploader ): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

                if ($imageFile) {
                    $fileName = $fileUploader->upload($imageFile);
                    $livre->setImageName($fileName);
                }
              $livre->setAjoutePar($this->getUser());

            $em->persist($livre);
            $em->flush();
  // ✅ EMAIL
        $email = (new TemplatedEmail())
            ->from('noreply@bookshelf.com')
            ->to('admin@bookshelf.com') // ou config
            ->subject('📗 Nouveau livre ajouté : ' . $livre->getTitre())
            ->htmlTemplate('emails/nouveau_livre.html.twig')
            ->context([
                'livre' => $livre,
                'user' => $this->getUser(),
            ]);

        $mailer->send($email);
            $this->addFlash('success', 'Livre ajouté avec succès !');

            return $this->redirectToRoute('app_livres');
        }

        return $this->render('livre/nouveau.html.twig', [
            'formulaire' => $form,
        ]);
    }



    // ✏️ MODIFIER LIVRE
    #[IsGranted('ROLE_USER')]
    #[Route('/livres/{id}/modifier', name: 'app_livre_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Livre $livre, Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
          if ($this->getUser() !== $livre->getAjoutePar() && !$this->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException('Accès refusé');
    }
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
$imageFile = $form->get('imageFile')->getData();

if ($imageFile) {

    // supprimer ancienne image
    if ($livre->getImageName()) {
        $fileUploader->remove($livre->getImageName());
    }

    // upload nouvelle
    $fileName = $fileUploader->upload($imageFile);
    $livre->setImageName($fileName);
}
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
    #[IsGranted('ROLE_USER')]
    #[Route('/livres/{id}/supprimer', name: 'app_livre_supprimer', methods: ['POST'])]
    public function supprimer(Livre $livre, Request $request, EntityManagerInterface $em  , FileUploader $fileUploader): Response
    {
        if ($this->getUser() !== $livre->getAjoutePar() && !$this->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException('Accès refusé');
    }
        if ($this->isCsrfTokenValid('supprimer_'.$livre->getId(), $request->request->get('_token'))) {
     // 🖼️ 1. SUPPRESSION IMAGE PHYSIQUE (AVANT DB)
        if ($livre->getImageName()) {
            $fileUploader->remove($livre->getImageName());
        }

        // 🗑️ 2. SUPPRESSION EN BASE
            $em->remove($livre);
            $em->flush();

            $this->addFlash('success', 'Livre supprimé avec succès !');

        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_livres');
    }


#[Route('/liste/ajouter/{id}', name: 'liste_add')]
public function addToList(int $id, RequestStack $requestStack): Response
{
    $session = $requestStack->getSession();

    // récupérer liste existante ou tableau vide
    $liste = $session->get('reading_list', []);

    if (!in_array($id, $liste)) {
        $liste[] = $id;
    }

    $session->set('reading_list', $liste);

    return $this->redirectToRoute('app_livres');
}


#[Route('/liste/supprimer/{id}', name: 'liste_remove')]
public function removeFromList(int $id, RequestStack $requestStack): Response
{
    $session = $requestStack->getSession();

    $liste = $session->get('reading_list', []);

    // supprimer l'id
    // $liste = array_filter($liste, fn($item) => $item != $id);
foreach ($liste as $key => $item) {
    if ($item == $id) {
        unset($liste[$key]);
    }
}
    $session->set('reading_list', $liste);

    return $this->redirectToRoute('ma_liste');
}



#[Route('/ma-liste', name: 'ma_liste')]
public function myList(RequestStack $requestStack, LivreRepository $repo): Response
{
    $session = $requestStack->getSession();

    $ids = $session->get('reading_list', []);

    $livres = $repo->findBy(['id' => $ids]);

    return $this->render('livre/ma_liste.html.twig', [
        'livres' => $livres
    ]);
}







}

    

