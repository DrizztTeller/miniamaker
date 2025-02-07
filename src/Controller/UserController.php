<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, UploaderService $us): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (true) {
                $image = $form->get('image')->getData();
                if ($image != null) {
                    $user->setImage(
                        $us->uploadFile($image, $user->getImage())
                    );
                }
            }
            $em->persist($user);
            $em->flush();

            // Redirection avec flash message
            $this->addFlash('success', 'Votre profil à été mis à jour');
            return $this->redirectToRoute('app_profile');
        }

        if (!$user->isVerified()) {
            $this->addFlash('warning', 'Validez votre email !');
        }
        return $this->render('user/index.html.twig', [
            'userForm' => $form,
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
