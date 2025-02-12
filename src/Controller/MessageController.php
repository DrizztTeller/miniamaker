<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Discussion;
use App\Repository\MessageRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class MessageController extends AbstractController
{
    public function __construct(
        private DiscussionRepository $dr,
        private MessageRepository $mr,
        private EntityManagerInterface $em,
    ) {}

    #[Route('/messages', name: 'app_message', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', []);
    }

    #[Route('/messages/{id}', name: 'app_message_show', methods: ['GET', 'POST'])]
    public function show($id): Response
    {
        return $this->render('message/show.html.twig', [
            'messages' => $this->mr->findByDiscussion($id, ['created_at' => 'DESC']),
        ]);
    }

    #[Route('/message/delete/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Message $message): Response
    {
        if ($message->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer ce message.");
        }

        $message->setStatus(false); // Marquer comme supprimé mais conserver en BDD
        $this->em->flush();

        return $this->redirectToRoute('app_message_show', ['id' => $message->getDiscussion()->getId()]);
    }

    #[Route('/message/new', name: 'app_message_new', methods: ['POST'])]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user) {
        throw $this->createAccessDeniedException("Vous devez être connecté pour envoyer un message.");
    }

    $content = trim($request->request->get('content'));
    
    if (empty($content)) {
        $this->addFlash('warning', 'Le message ne peut pas être vide.');
        return $this->redirectToRoute('app_message_show', ['id' => $request->query->get('discussion_id')]);
    }

    // Récupérer la discussion actuelle (assure-toi que le paramètre est bien passé)
    $discussionId = $request->query->get('discussion_id');
    $discussion = $em->getRepository(Discussion::class)->find($discussionId);

    if (!$discussion) {
        throw $this->createNotFoundException("Discussion introuvable.");
    }

    $message = new Message();
    $message->setAuthor($user);
    $message->setContent($content);
    $message->setCreatedAt(new \DateTimeImmutable());
    $message->setStatus(true); // Message actif (envoyé)
    $message->setDiscussion($discussion);

    $em->persist($message);
    $em->flush();

    return $this->redirectToRoute('app_message_show', ['id' => $discussionId]);
}

    
}
