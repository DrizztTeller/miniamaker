<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class LandingPageController extends AbstractController{
    #[Route('/landing/add', name: 'lp_add', methods:['GET', 'POST'])]
    public function add(): Response
    {
        $user = $this->getUser()->getRoles()[0];

        if ($user !== 'ROLE_PRO' && $user !== 'ROLE_AGENT') {
            return $this->redirectToRoute('app_detail');
        }

        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController',
        ]);
    }
}
