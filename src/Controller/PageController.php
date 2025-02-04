<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController{
    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->render('page/lp.html.twig');
        }

        return $this->render('page/homepage.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
}
