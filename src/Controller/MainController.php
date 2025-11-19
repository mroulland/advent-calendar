<?php

namespace App\Controller;

use App\Entity\Ranking;
use App\Entity\Calendar;
use App\Entity\AdventCalendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $manager): Response
    {

        $adventCalendar = $manager->getRepository(AdventCalendar::class)->findForDisplay();
        $calendar = $manager->getRepository(Calendar::class)->findBy(
            ['adventCalendar' => $adventCalendar]
        );
        shuffle($calendar);

        // on récupère le ranking global après traitement
        $rankings = $manager->getRepository(Ranking::class)->findGlobalRanking($adventCalendar);

        return $this->render('main/index.html.twig', [
            'calendar' => $calendar,
            'rankings' => $rankings
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/galerie', name: 'app_galerie')]
    public function galerie(EntityManagerInterface $manager): Response
    {
        $currentCalendar = $manager->getRepository(AdventCalendar::class)->findForDisplay();
        $currentYear = $currentCalendar->getYear();

        $rankings = $manager->getRepository(Ranking::class)->findCurrentYearParticipations($currentYear);
        $oldRankings = $manager->getRepository(Ranking::class)->findPastParticipations($currentYear);
         
        return $this->render('main/galerie.html.twig', [
            'rankings' => $rankings,
            'oldRankings' => $oldRankings
        ]);
    }
}
