<?php namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BookShelfExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // transforme une date en "il y a X temps"
            new TwigFilter('time_ago', [$this, 'timeAgo']),

            // calcule le temps de lecture
            new TwigFilter('reading_time', [$this, 'readingTime']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            // génère un badge HTML disponibilité
            new TwigFunction('book_status_badge', [$this, 'bookStatusBadge'], [
                'is_safe' => ['html'] // important: autorise HTML
            ]),
        ];
    }

    // -------------------------
    // FILTRE 1 : time_ago
    // -------------------------
    public function timeAgo(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->y > 0) return "il y a {$diff->y} an(s)";
        if ($diff->m > 0) return "il y a {$diff->m} mois";
        if ($diff->d > 0) return "il y a {$diff->d} jour(s)";
        if ($diff->h > 0) return "il y a {$diff->h} heure(s)";

        return "à l'instant";
    }

    // -------------------------
    // FILTRE 2 : reading_time
    // -------------------------
    public function readingTime(int $pages): string
    {
        $hours = intdiv($pages, 30);
        $minutes = ($pages % 30) * 2;

        return $hours . "h" . $minutes . " de lecture";
    }

    // -------------------------
    // FONCTION : badge dispo
    // -------------------------
    public function bookStatusBadge(bool $disponible): string
    {
        if ($disponible) {
            return '<span class="badge bg-success">Disponible</span>';
        }

        return '<span class="badge bg-danger">Indisponible</span>';
    }
}