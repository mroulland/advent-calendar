<?php

namespace App\Controller\Admin;

use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Entity\QuizChallenge;
use App\Entity\PhotoChallenge;
use App\Repository\ChallengeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/challenge')]
final class ChallengeController extends AbstractController
{
    #[Route(name: 'app_admin_challenge_index', methods: ['GET'])]
    public function index(ChallengeRepository $challengeRepository): Response
    {
        return $this->render('admin/challenge/index.html.twig', [
            'challenges' => $challengeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_challenge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $challenge = new Challenge();
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->get('type')->getData();
            $challenge = null;
            if ($type === 'quiz') {
                $challenge = new QuizChallenge();
                $challenge->setQuestions($form->get('questions')->getData());
                $challenge->setAnswers($form->get('answers')->getData());

            } elseif ($type === 'photo') {
                $challenge = new PhotoChallenge();
                $challenge->setUploadDirectory($form->get('uploadDirectory')->getData());
            }

            if ($challenge) {
                // Récupère les données générales depuis le formulaire
                $challenge->setDescription($form->get('description')->getData());
                $challenge->setTitle($form->get('title')->getData());

                // Sauvegarde dans la base de données
               
                $entityManager->persist($challenge);
                $entityManager->flush();

                $this->addFlash('success', 'Le défi a été créé avec succès !');
            }

            return $this->redirectToRoute('app_admin_challenge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/challenge/new.html.twig', [
            'challenge' => $challenge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_challenge_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Challenge $challenge, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le défi a bien été mis à jour !');
            //return $this->redirectToRoute('app_admin_challenge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/challenge/edit.html.twig', [
            'challenge' => $challenge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_challenge_delete', methods: ['POST'])]
    public function delete(Request $request, Challenge $challenge, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$challenge->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($challenge);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_challenge_index', [], Response::HTTP_SEE_OTHER);
    }
}