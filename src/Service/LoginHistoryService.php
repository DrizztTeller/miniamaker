<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\LoginHistory;
use DeviceDetector\DeviceDetector;
use Doctrine\ORM\EntityManagerInterface;

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
  public function __construct(readonly private EntityManagerInterface $em) {}

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

    $this->em->persist($loginHistory); // transforme en requete SQL 
    $this->em->flush();
  }
}
