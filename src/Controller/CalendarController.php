<?php

namespace App\Controller;

use DateTime;
use App\Form\QuizType;
use App\Entity\Ranking;
use App\Form\PhotoType;
use App\Entity\Calendar;
use App\Entity\QuizChallenge;
use App\Entity\PhotoChallenge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/calendar')]
class CalendarController extends AbstractController
{

    #[Route('/{id}', name: 'app_calendar_index', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function index(?Calendar $calendar, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        if(!isset($calendar) || !$calendar->getChallenge()){
            return $this->redirectToRoute('app_main');
        }

        $user = $this->getUser();
        $challenge = $calendar->getChallenge();
        
        $already_done = $manager->getRepository(Ranking::class)->isUserAlreadyDone($user, $challenge) ? true : false;
        $results = [];

        if($challenge instanceof QuizChallenge)
        {
            $type = "quiz";
            $questions = $challenge->getQuestions();
            $form = $this->createForm(QuizType::class, null, ['questions' => $questions]);
        }
        elseif($challenge instanceof PhotoChallenge)
        {
            $type = "photo";
            $form = $this->createForm(PhotoType::class);
        }else
        {
            return $this->redirectToRoute('app_main');
        }

        $form->handleRequest($request);


        if (!$already_done && $form->isSubmitted() && $form->isValid()) {
            
            $ranking = new Ranking();
            $ranking->setDate(new DateTime('now'));
            $ranking->setUser($user);
            $ranking->setChallenge($challenge);

            if($challenge instanceof QuizChallenge)
            {
                $correctAnswers = $challenge->getAnswers();
                $submittedAnswers = $form->getData();
                $ranking->setDetails($submittedAnswers);

                // Validation des réponses : 
                $results = $this->validateAnswers($submittedAnswers, $correctAnswers);
                $points = $results["points"];
            }
            elseif($challenge instanceof PhotoChallenge)
            {
                
                $pictureFile = $form->get('pictureFile')->getData();
                
                if($pictureFile){
                    $pictureFilename = $slugger->slug($challenge->getTitle()) . '-'. $ranking->getUser()->getId(). '.'. $pictureFile->guessExtension();
                    $directory = $challenge->getUploadDirectory();
                    try {
                        $pictureFile->move(
                            $this->getParameter('kernel.project_dir') . '/assets/imgs/'.$directory, $pictureFilename);
                        
                        $ranking->setDetails([$pictureFilename]);
                        $points = 5;
                            
                    } catch (FileException $e) {
                        throw new \Exception('Une erreur est survenue lors du téléchargement de l\'image.');
                    }
                }
            }
            $ranking->setPoints($points);
            $manager->persist($ranking);
            $manager->flush();
        }

        // on récupère le ranking global après traitement
        $rankings = $manager->getRepository(Ranking::class)->findByChallenge($challenge);

        return $this->render('calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
            'calendar' => $calendar,
            'already_done' => $already_done,
            'type' => $type,
            'form' => $form,
            'rankings' => $rankings,
            'results' => $results
        ]);
    }

    private function validateAnswers($submittedAnswers, $correctAnswers)
    {
        $results = [];
        $points = 0;
        foreach($submittedAnswers as $key => $value)
        {
            if(is_array($correctAnswers[$key])){
                foreach($correctAnswers[$key] as $answer){
                    if(strtolower($answer) == strtolower($value)){
                        $results[$key]["status"] = true;
                        break;
                    }
                    $results[$key]["status"] = false;
                    $points++;
                }
            }
            else{
                if(strtolower($correctAnswers[$key]) == strtolower($value)){
                    $results[$key]["status"] = true;
                    $points++;
                }
                else{
                    $results[$key]["status"] = false;
                }
            }

            $results[$key]["submitted"] = $value;
            $results[$key]["correct"] = is_array($correctAnswers[$key]) ? $correctAnswers[$key][0] : $correctAnswers[$key];
        }
        $results["points"] = $points;
        
        return $results;
    }

}
