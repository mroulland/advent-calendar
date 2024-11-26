<?php

namespace App\Controller;

use App\Entity\Ranking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $manager): Response
    {
        // on récupère le ranking global après traitement
        $rankings = $manager->getRepository(Ranking::class)->findGlobalRanking();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'rankings' => $rankings
        ]);
    }

    
}