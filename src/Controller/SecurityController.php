<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\PasswordResetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
  
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePictureFile = $form->get('profilePictureFile')->getData();

            if ($profilePictureFile) {
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();
    
                try {
                    $profilePictureFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/profile_pictures',
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Une erreur est survenue lors du téléchargement de l\'image.');
                }
                $user->setProfilePicture($newFilename);
            }

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/profil', name: 'app_profile')]
    public function profile(): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/profile.html.twig', [

        ]);
    }

    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $em,
        PasswordResetService $resetService,
        \Symfony\Component\Mailer\MailerInterface $mailer,
        EmailService $emailService
    ): Response {

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);


            if ($user) {
                // 1. Génération du token
                $token = $resetService->generateResetToken($user);

                $em->flush();

                // 2. Envoi du mail
                $resetUrl = $this->generateUrl(
                    'app_reset_password',
                    ['token' => $token],
                    \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
                );


                
                // $emailMessage = (new Email())
                //     ->from('calendrier-avent@pixelsandcookies.fr')
                //     ->to($user->getEmail())
                //     ->subject('Réinitialisation de votre mot de passe')
                //     ->text('Bonjour, ceci est un mail automatique envoyé depuis Symfony !')
                //     ->html("
                //         <p>Bonjour,</p>
                //         <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                //         <p><a href='$resetUrl'>$resetUrl</a></p>
                //         <p>Ce lien expire dans 1 heure.</p>
                //     ");

                // $mailer->send($emailMessage);
            }

            //$this->addFlash('success', 'Si un compte existe, un email a été envoyé. <br/> Vérifiez votre boîte de réception ainsi que votre dossier spam.');
            $this->addFlash('success', '<p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p><p><a href='.$resetUrl.'>'.$resetUrl.'</a></p>');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reinitialiser-mot-de-passe/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        PasswordResetService $resetService,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var User|null $user */
        //$user = $em->getRepository(User::class)->findOneBy(['tokenHash' => $token]);
        $user = $userRepository->findOneByValidResetToken($token);

        if (!$user ) {
            $this->addFlash('danger', 'Le lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');

            if (strlen($newPassword) < 6) {
                $this->addFlash('danger', 'Le mot de passe doit faire au moins 6 caractères.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }

            // Mise à jour du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword($user, $newPassword)
            );

            // Suppression du token
            $user->setTokenHash(null);
            $user->setTokenHashExpiresAt(null);

            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
        ]);
    }




}
