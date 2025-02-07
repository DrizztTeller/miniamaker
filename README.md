Miniamaker : application de mise en relation entre professionnels et clients. App elitiste, exclusif : les professionnels payent pour être présent sur l'app et les clients payent pour accéder à l'app et chercher un professionnel. 
Recherche par rapport à la thématique

USER : 
- ROLE_ADMIN
- ROLE_AGENT : agent d'influenceurs 
- ROLE_PRO : prestataire de services
- ROLE_CLIENT : celui qui vient chercher un service

Sign in : 
- PRO : -> CHECK STATUS -> PORTFOLIO -> OFFRE -> DM(s)
- CLIENT : -> SEARCH -> ABONNEMENT -> DM(s)

Schéma de la BDD : 
- https://drawsql.app/teams/agiliteach/diagrams/miniamaker
- https://drawsql.app/teams/drizztteller/diagrams/miniamaker

- symfony new miniamaker --webapp   
- symfony console make:user    
- symfony console make:s:f
- symfony console make:registration-form
- composer require symfonycasts/verify-email-bundle
- symfony console messenger:consume async -vv
- composer require matomo/device-detector
- composer require symfony/ux-dropzone
- 