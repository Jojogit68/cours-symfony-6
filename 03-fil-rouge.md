# Fil rouge de notre application
Pas à pas, nous allons créer une plateforme type __petites annonces__.  
Ce n'est pas forcément un sujet très "drôle" mais ce type d'application a 
le mérite de regrouper des fonctionnalités diverses et c'est un bon point de 
départ pour commencer avec Symfony. 

Si tu n'as pas d'idée de produit, je te proposes de créer un site
dédié à la vente et achat pour les collectionneurs de 
[canards](https://www.youtube.com/watch?v=-w0qTvjydik) en plastique.
Pourquoi des canards en plastique ? Car en informatique il y a une méthode qui 
a fait ses preuves, qui se nomme la ["Méthode du canard en plastique"](https://fr.wikipedia.org/wiki/M%C3%A9thode_du_canard_en_plastique)
et qui consiste à expliquer ses problèmes informatique à... un canard.

## Fonctionnalités:
- Création d'annonces, avec photo, prix, description ;
- Affichage en liste des annonces avec pagination ;
- Modification d'une annonce par son créateur ;
- Suppression d'une annonce par son créateur ;
- Espace d'administration des utilisateurs et des annonces ;
- Espace personnel permettant de gérer ses annonces et son profil ;
- Système de tag simple pour les annonces ;
- Recherche des annonces ;
- Système d'inscription et de login.

## Installer Symfony
Tout est indiqué dans la [documentation](https://symfony.com/doc/current/setup.html#creating-symfony-applications),
n'hésites pas à la suivre.

- Depuis ton terminal, rends-toi dans ton environnement de développement
    (ou clic droit dans un dossier _git bash here_ pour git bash), 
    (par exemple dans le dossier __www__ de __Wamp__ ou de __Laragon__), et tapez :  
    ``` console
    symfony new le_nom_de_votre_projet --webapp
    # ou
    composer create-project symfony/website-skeleton le_nom_de_votre_projet
    # vous pouvez remplacer le_nom_de_votre_projet par 'duckzon', par exemple
    ```

    _Pour plus d'infos sur l'installation de Symfony : [https://symfony.com](https://symfony.com)_

-  Va dans ton dossier   
```cd le_nom_de_votre_projet```
- Ouvre le dossier contenant les fichiers de Symfony dans ton éditeur préféré. Pour VS Code, il suffira de taper ```code ./``` dans un terminal. 

  - Lance le serveur de développement :  
      ``` console
      symfony server:start
      # résultat
      [OK] Web server listening on https://127.0.0.1:8000 (PHP FPM 7.3.16)
      ```  
      Laisse ce terminal de côté. Si tu le fermes, le serveur s'arrêtera ! Pour arrêter le serveur, appuie sur ```ctrl+c``` depuis ce terminal.  
      Cette commande permet de lancer le [serveur interne de PHP](https://www.php.net/manual/fr/features.commandline.webserver.php). 
      Celui-ci va interpréter les fichiers PHP et agir comme un petit serveur web.
      À utiliser __seulement__ pour développer en local !  
      __Wamp__, __Laragon__ et consorts ne nous seront utiles que pour le serveur de base de donnée. 
- Tape cette url depuis votre navigateur préféré:
[http://127.0.0.1:8000](http://127.0.0.1:8000). Tu devrais voir quelque chose comme __Welcome to Symfony [version]__. Si c'est le cas :   
!['Well done Padawan'](https://media.giphy.com/media/9g8PH1MbwTy4o/giphy.gif)
## Structure des dossiers
- __config/__  
Contiens... la configuration! Permet de configurer les services, les packages...  
- __src/__  
Tout le code PHP.  
- __templates/__  
Tous les templates Twig.  
- __bin/__  
Fichiers exécutables nécessaires à la console et autres.  
- __var/__  
C'est là que sont stockés les fichiers créés automatiquement, 
comme les fichiers cache (var / cache /) et les logs (var / log /).  
- __vendor/__  
Des bibliothèques tierces (c'est-à-dire "fournisseur"). 
Celles-ci sont téléchargées via le gestionnaire de paquets Composer.  
- __public/__  
C'est le dossier accessible depuis un navigateur: 
il contiendra tous les fichiers accessibles aux visiteurs 
(les images, les ressources CSS, JS, etc...).

Tu peux noter qu'un fichier ```composer.json``` a été créé à la racine
du projet. Celui-ci décrit entre autres les bibliothèques à installer pour le projet grâce à _Composer_.
À chaque fois que tu vas installer une nouvelle bibliothèque, une ligne y sera ajoutée.

Ce fichier, ainsi que les fichiers ```composer.lock``` et ```symfony.lock``` permettront aux
autres développeurs·euses participant au projet d'installer les mêmes librairies. Nous y reviendrons.

N'hésite pas à jeter un œil et en apprendre plus sur [_Composer_](https://www.grafikart.fr/tutoriels/composer-480)
