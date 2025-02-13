<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, UploaderService $us, UserPasswordHasherInterface $uphi): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pwd = $uphi->isPasswordValid($user, $form->get('password')->getData());;
            if ($pwd) {
                $image = $form->get('image')->getData();
                if ($image != null) {
                    $user->setImage(
                        $us->uploadFile($image, $user->getImage())
                    );
                }

                $em->persist($user);
                $em->flush();

                // Redirection avec flash message
                $this->addFlash('success', 'Votre profil à été mis à jour');
            } else {
                $this->addFlash('error', 'Une erreur est survenue');
            }

            return $this->redirectToRoute('app_profile');
        }

        if (!$user->isVerified()) {
            $this->addFlash('warning', 'Validez votre email !');
        }

        if ($user->getSubscription() !== null) {
            $subs = $user->getSubscription();
            if (!$subs->isActive()) {
                $now = new \DateTime();
                $dateMax = (clone $subs->getCreatedAt())->modify('+20 minutes');
                if ($now > $dateMax) {
                    $em->remove($subs);
                    $em->flush();
                }
            }
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

            if (!$user->isVerified()) {
                $this->addFlash('success', "Il ne vous reste qu'à vérifier votre email pour compléter votre profil");
            } else {
                $this->addFlash('success', 'Votre profil est complété');
            }
        } else {
            $this->addFlash('error', 'Vous devez remplir tous les champs');
        }

        return $this->redirectToRoute('app_profile');
    }
}
