<?php

namespace App\Controller;

use App\Entity\Detail;
use App\Form\DetailFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DetailController extends AbstractController
{
    #[Route('/detail', name: 'app_detail')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $detail = new Detail();
        $detail->setPro($this->getUser());
        $form = $this->createForm(DetailFormType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();

            if (!empty($role)) {
                if ($role == 'Agent') {
                    $this->getUser()->setRoles(["ROLE_AGENT"]);
                } elseif ($role == 'Pro') {
                    $this->getUser()->setRoles(["ROLE_PRO"]);
                }
            }
            
            $em->persist($this->getUser());
            $em->persist($detail);
            $em->flush();
            
            // Redirection avec flash message
            $this->addFlash('success', 'Félicitations ! Votre fiche est compléte.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('detail/form.html.twig', [
            'detailForm' => $form
        ]);
    }
}
