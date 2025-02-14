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
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(Request $request, UploaderService $us, UserPasswordHasherInterface $uphi): Response
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

                $this->em->persist($user);
                $this->em->flush();

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
            $now = new \DateTime();

            $remove = false;

            if (!$subs->isActive()) {
                $dateMax = (clone $subs->getCreatedAt())->modify('+20 minutes');
                $remove = $now > $dateMax;
            } else {
                $subsEnd = (clone $subs->getUpdatedAt())->modify('+1 year');
                $remove = $now > $subsEnd;
            }

            if ($remove) {
                $this->em->remove($subs);
                $this->em->flush();
            }
        }

        return $this->render('user/index.html.twig', [
            'userForm' => $form,
        ]);
    }


    #[Route('/complete', name: 'app_complete', methods: ['POST'])]
    public function complete(Request $request): Response
    {
        $username = $request->getPayload()->get('username');
        $fullname = $request->getPayload()->get('fullname');
        // dd($username);

        if (!empty($username) && !empty($fullname)) {
            $user = $this->getUser();
            $user->setUsername($username)
                ->setFullname($fullname);
            $this->em->persist($user);
            $this->em->flush();

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
