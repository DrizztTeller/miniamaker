<?php

namespace App\Controller;

use App\Repository\DiscussionRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class MessageController extends AbstractController
{
    public function __construct(
        private DiscussionRepository $dr,
        private MessageRepository $mr,
    ){}

    #[Route('/messages', name: 'app_message', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [

        ]);
    }

    #[Route('/messages/{id}', name: 'app_message_show', methods: ['GET', 'POST'])]
    public function show($id): Response
    {
        return $this->render('message/show.html.twig', [
            'messages' => $this->mr->findByDiscussion($id, ['created_at' => 'DESC']),
        ]);
    }
}
