<?php 
namespace App\Command;

use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;




#[AsCommand(
    name: 'app:bookshelf:stats',
    description: 'Affiche les statistiques de la bibliothèque'
)]
class BookShelfStatsCommand extends Command
{
    public function __construct(
        private LivreRepository $livreRepo,
        private AuteurRepository $auteurRepo,
        private GenreRepository $genreRepo
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        // option --detail = afficher plus d'infos
        $this->addOption('detail', null, InputOption::VALUE_NONE);

        // option --format=json (bonus)
        $this->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Format de sortie');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // -------------------------
        // RÉCUPÉRATION DONNÉES
        // -------------------------
        $totalLivres = $this->livreRepo->count([]);
        $disponibles = $this->livreRepo->count(['disponible' => true]);
        $indisponibles = $totalLivres - $disponibles;

        $nbAuteurs = $this->auteurRepo->count([]);
        $nbGenres = $this->genreRepo->count([]);

        // -------------------------
        // AFFICHAGE PRINCIPAL
        // -------------------------
        $io->title('📊 Statistiques BookShelf');

        $io->table(
            ['Indicateur', 'Valeur'],
            [
                ['Total livres', $totalLivres],
                ['Disponibles', $disponibles],
                ['Indisponibles', $indisponibles],
                ['Auteurs', $nbAuteurs],
                ['Genres', $nbGenres],
            ]
        );

        // -------------------------
        // MODE DETAIL
        // -------------------------
        if ($input->getOption('detail')) {

            $io->section('📚 Détail par genre');

            $genres = $this->genreRepo->findAll();

            $rows = [];

            foreach ($genres as $genre) {
                $rows[] = [
                    $genre->getNom(),
                    count($genre->getLivres())
                ];
            }

            $io->table(['Genre', 'Nombre livres'], $rows);
        }

        // -------------------------
        // MESSAGE FINAL
        // -------------------------
        $io->success('Statistiques générées avec succès');

        return Command::SUCCESS;
    }
}