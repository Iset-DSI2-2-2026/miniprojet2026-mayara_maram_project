<?php

namespace App\Repository;

use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

public function findByFilters(?string $titre, ?Genre $genre, ?bool $disponible, ?Tag $tag): array
{
    $qb = $this->createQueryBuilder('l');

    // 🔍 Filtre titre (recherche partielle)
    if ($titre) {
        $qb->andWhere('l.titre LIKE :titre')
           ->setParameter('titre', '%' . $titre . '%');
    }

    // 📚 Filtre genre
    if ($genre) {
        $qb->andWhere('l.genre = :genre')
           ->setParameter('genre', $genre);
    }

    // ✅ Filtre disponibilité
    if ($disponible !== null) {
        $qb->andWhere('l.disponible = :dispo')
           ->setParameter('dispo', $disponible);
    }

    // 🏷️ Filtre tag (ManyToMany)
    if ($tag) {
        $qb->innerJoin('l.tags', 't')
           ->andWhere('t = :tag')
           ->setParameter('tag', $tag);
    }

    // 📅 Tri des résultats (plus récents en premier)
    return $qb->orderBy('l.datePublication', 'DESC')
              ->getQuery()
              ->getResult();
}




public function findLastAdded(int $limit = 5): array
{
    return $this->createQueryBuilder('l')
        ->orderBy('l.id', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}











//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
