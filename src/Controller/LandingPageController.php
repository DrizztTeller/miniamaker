<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LandingPageController extends AbstractController{
    #[Route('/landing/add', name: 'lp_add')]
    public function add(): Response
    {
        $user = $this->getUser();

        if (in_array('ROLE_AGENT', $user->getRoles()) || in_array('ROLE_PRO', $user->getRoles())) {
            return $this->redirectToRoute('app_detail');
        }

        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController',
        ]);
    }
}
