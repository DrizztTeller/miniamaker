<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\LoginHistory;
use DeviceDetector\DeviceDetector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Classe de gestion de l'historique de connexion des utilisateurs
 */
class LoginHistoryService
{
  // Methode 1
  // private $em;
  // public function __construct(readonly EntityManagerInterface $emi) 
  // {
  //   $this->em =$emi;
  // }


  // Methode 2
  // readonly : permet que de lire les methodes, les utiliser mais pas les suppr ou les modifier : on agit en tant que spectateur: limiter ce qu'on peut faire
  public function __construct(readonly private EntityManagerInterface $em, private MailerInterface $mailer) {}

  public function addHistory(User $user, string $userAgent, string $ip): void
  {

    $deviceDetector = new DeviceDetector($userAgent);
    $deviceDetector->parse();  //parser = regarder dedans

    $loginHistory = new LoginHistory();
    $loginHistory->setUser($user)
      ->setIpAddress($ip)
      ->setDevice($deviceDetector->getDeviceName())
      ->setOs($deviceDetector->getOs()['name'])
      ->setBrowser($deviceDetector->getClient()['name']);

    $this->em->persist($loginHistory); // prepare la requete SQL pour insérer l'objet 
    $this->em->flush(); // execute la requete

    // Création de l'email avec un template Twig
    $email = (new TemplatedEmail())
      ->from('no-reply@miniamaker.com')
      ->to($user->getEmail())
      ->subject('Nouvelle connexion détectée')
      ->htmlTemplate('components/login_history_email.html.twig')
      ->context([
        'user' => $user,
        'ip' => $ip,
        'device' => $loginHistory->getDevice(),
        'os' => $loginHistory->getOs(),
        'browser' => $loginHistory->getBrowser(),
      ]);

    try {
      $this->mailer->send($email);
    } catch (TransportExceptionInterface $e) {
      throw new \Exception('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
    }
  }
}
