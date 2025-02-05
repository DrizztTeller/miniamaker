<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/complete', name: 'app_complete', methods: ['POST'])]
    public function complete(Request $request, EntityManagerInterface $em): Response
    {
        $username = $request->getPayload()->get('username');    
        $fullname = $request->getPayload()->get('fullname'); 
        // dd($username);

        if (!empty($username) && !empty($fullname)) {
            $user = $this->getUser();
            $user->setUsername($username)
                ->setFullname($fullname);
            $em->persist($user);
            $em->flush();
            
            $this->addFlash('success', 'Votre profil est complété');
        } else {

            $this->addFlash('error', 'Vous devez remplir tous les champs');
        }

        return $this->redirectToRoute('app_profile');
    }
}
