<?php

namespace App\Controller;

use App\Repository\DiscussionRepository;
use App\Repository\LandingPageRepository;
use App\Service\LoginHistoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(Request $request, LoginHistoryService $lHS, LandingPageRepository $lpR, DiscussionRepository $dR): Response
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
                return $this->render('user/complete.html.twig');
            }

            $dateThreshold = new \DateTime();
            $dateThreshold->modify('-1 day');

            $discussions = $dR->FindAllRecentDiscussions($this->getUser(), $dateThreshold);
            $landingPages = $lpR->findAll();
            $landingPagesPro = $lpR->findByUserRole('ROLE_PRO');
            $landingPagesAgent = $lpR->findByUserRole('ROLE_AGENT');

            return $this->render('page/homepage.html.twig', [
                'discussions' => $discussions,
                'landingPages' => $landingPages,
                'landingPagesPro' => $landingPagesPro,
                'landingPagesAgent' => $landingPagesAgent,
            ]);
        }
    }
}
