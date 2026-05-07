<?php namespace App\Service;

use App\Repository\LivreRepository;

class BibliothequeStats
{
    public function __construct(private LivreRepository $livreRepository) {}

    // Nombre total de livres
    public function getTotalLivres(): int
    {
        return $this->livreRepository->count([]);
    }

    // Nombre de livres disponibles
    public function getLivresDisponibles(): int
    {
        return $this->livreRepository->count(['disponible' => true]);
    }

    // Nombre de livres par genre
    public function getLivresParGenre(): array
    {
        $qb = $this->livreRepository->createQueryBuilder('l')
            ->select('g.nom as genre, COUNT(l.id) as total')
            ->join('l.genre', 'g')
            ->groupBy('g.nom');

        return $qb->getQuery()->getResult();
    }

    // Temps de lecture total
    public function getTempsLectureTotal(): float
    {
        $livres = $this->livreRepository->findAll();

        $totalPages = 0;

        foreach ($livres as $livre) {
            $totalPages += $livre->getNbPages();
        }

        return round($totalPages / 30, 2); // 30 pages / heure
    }
}