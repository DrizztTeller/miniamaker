<?php

namespace App\Controller;

use App\Service\LoginHistoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(Request $request, LoginHistoryService $lHS): Response
    {
        if (!$this->getUser()) {
            return $this->render('page/lp.html.twig');
        } else {
            $requestArray = [
                "fromLogin" => $this->getParameter('APP_URL') . $this->generateUrl('app_login'),
                "referer" => $request->headers->get('referer'),
                "user-agent" => $request->headers->get('user-agent'),
                "ip" => $request->getClientIp(),

            ];

            if ($requestArray['referer'] === $requestArray['fromLogin']) {
                $lHS->addHistory($this->getUser(), $requestArray['user-agent'], $requestArray['ip']);
            }

            if (!$this->getUser()->isComplete()) {
                return $this->render('page/complete.html.twig');
            }
            
            return $this->render('page/homepage.html.twig', [
                'controller_name' => 'PageController',
            ]);
        }
    }
}
