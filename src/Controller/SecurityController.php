<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    // #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    // public function login(AuthenticationUtils $authenticationUtils): Response
    // {

    //      //Si déjà co, redirection vers page profile
    //      if ($this->getUser()) {
    //         return $this->redirectToRoute('app_profile');
    //     }

    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();

    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('security/login.html.twig', [
    //         'last_username' => $lastUsername,
    //         'error' => $error,
    //     ]);
    // }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function requestLoginLink(LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request, MailerInterface $mailer): Response
    {
        // check if form is submitted
        if ($request->isMethod('POST')) {
            // load the user in some way (e.g. using the form input)
            $email = $request->getPayload()->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $loginLink = $loginLinkDetails->getUrl();

            // ... send the link and return a response (see next section)
            $email = (new Email())
            ->from('contact@miniamaker.fr')
            ->to($user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Votre lien de connexion')
            ->text('Votre lien de connexion')
            ->html("<p>Cliquer pour vous connecter :  <br>" . $loginLink . "</p>");

            $mailer->send($email);
            $notif = "Message envoyé";

            return $this->render('security/login.html.twig', [
                'notif' => $notif
            ]);
        }

        // if it's not submitted, render the form to request the "login link"
        return $this->render('security/login.html.twig');
    }

    #[Route('/login_check', name: 'login_check')]
    public function check(): never
    {
        throw new \LogicException('This code should never be reached');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
