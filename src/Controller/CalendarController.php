<?php

namespace App\Controller;

use DateTime;
use App\Form\QuizType;
use App\Entity\Ranking;
use App\Form\PhotoType;
use App\Entity\Calendar;
use App\Form\HangmanType;
use App\Entity\QuizChallenge;
use App\Entity\PhotoChallenge;
use App\Form\ParticipationType;
use App\Entity\HangmanChallenge;
use App\Entity\ParticipationChallenge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/calendrier')]
class CalendarController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'app_calendar_index', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function index(?Calendar $calendar, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {

        if(!isset($calendar) || !$calendar->getChallenge() || 
            ($calendar->getDate() > new DateTime('now') && $this->denyAccessUnlessGranted('ROLE_ADMIN'))){
            return $this->redirectToRoute('app_main');
        }

        $user = $this->getUser();
        $challenge = $calendar->getChallenge();
        
        $already_done = $manager->getRepository(Ranking::class)->isUserAlreadyDone($user, $challenge) ? true : false;

        $params = [
            'controller_name' => 'CalendarController',
            'calendar' => $calendar,
            'already_done' => $already_done,
            'type' => null,
            'form' => null,
            'rankings' => null,
            'points' => "",
        ];
        
        if($challenge instanceof QuizChallenge)
        {
            $params['type'] = "quiz";
            $questions = $challenge->getQuestions();

            $params['form'] = $this->createForm(QuizType::class, null, ['questions' => $questions]);
        }
        elseif($challenge instanceof PhotoChallenge)
        {
            $params['type'] = "photo";
            $params['form'] = $this->createForm(PhotoType::class);
        }
        elseif($challenge instanceof ParticipationChallenge)
        {
            $params['type'] = "participation";
            $questions = $challenge->getQuestions();
            $params['form'] = $this->createForm(ParticipationType::class, null, ['questions' => $questions]);
        }
        elseif($challenge instanceof HangmanChallenge)
        {
            $params['type'] = "hangman";
            $params['word'] = $challenge->getWord();
            $params['hint'] = $challenge->getHint();
            $params['maxErrors'] = $challenge->getMaxErrors();

            $params['form'] = $this->createForm(HangmanType::class);       
        }else
        {
            return $this->redirectToRoute('app_main');
        }

        $params['form']->handleRequest($request);

        if (!$already_done && $params['form']->isSubmitted() && $params['form']->isValid()) {
            
            $ranking = new Ranking();
            $ranking->setDate(new DateTime('now'));
            $ranking->setUser($user);
            $ranking->setChallenge($challenge);

            if($challenge instanceof QuizChallenge)
            {
                $submittedAnswers = $params['form']->getData();
                $ranking->setDetails($submittedAnswers);

                // Validation des réponses : 
                $points = $this->validateAnswers($submittedAnswers, $questions);

            }
            elseif($challenge instanceof ParticipationChallenge)
            {
                
                $submittedAnswers = $params['form']->getData();
                $ranking->setDetails($submittedAnswers);

                $points = $questions["points"];
            }
            elseif($challenge instanceof PhotoChallenge)
            {
                
                $pictureFile = $params['form']->get('pictureFile')->getData();
                
                if($pictureFile){
                    $pictureFilename = $slugger->slug($challenge->getTitle()) . '-'. $ranking->getUser()->getId(). '.'. $pictureFile->guessExtension();
                    $directory = $challenge->getUploadDirectory();
                    try {
                        $pictureFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/challenges/'.$directory, $pictureFilename);
                        $ranking->setDetails([$pictureFilename]);
                        $points = 5;
                            
                    } catch (FileException $e) {
                        throw new \Exception('Une erreur est survenue lors du téléchargement de l\'image.');
                    }
                }
            }
            elseif($challenge instanceof HangmanChallenge)
            {
                
                $submittedAnswers['attempts'] = $params['attempts'] = $params['form']->get('attempts')->getData();
                $submittedAnswers['word'] = $params['form']->get('word')->getData();
                $ranking->setDetails($submittedAnswers);

                $points = 5;

                // On veut donner des points bonus à la personne qui a le moins de tentatives

            }

            // Si il s'agit de la première participation au challenge, on ajoute un bonus de 2 points
            if(!$manager->getRepository(Ranking::class)->findByChallenge($challenge)) $points += 2;
            
            $params['points'] = $points;
            $ranking->setPoints($points);
            $manager->persist($ranking);
            $manager->flush();
        }

        // on récupère le ranking global après traitement
        $params['rankings'] = $manager->getRepository(Ranking::class)->findByChallenge($challenge);

        return $this->render('calendar/index.html.twig', $params);
    }

    private function validateAnswers($submittedAnswers, $questions)
    {
        $points = 0;

        foreach($submittedAnswers as $key => $value)
        {
            
            if(isset($questions[$key]['answer']))
            {
                $answer = $questions[$key]['answer'];
                foreach($answer as $a){
                    if(strtolower($a) == strtolower($value)){
                        $points += $questions[$key]['points'];
                        break;
                    }
                }
            }
            else
            {
                $points += $questions[$key]['points'];
            }
            
        }
        return $points;
    }
}
