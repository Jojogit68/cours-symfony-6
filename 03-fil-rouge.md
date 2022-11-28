# Fil rouge de notre application
Pas à pas, nous allons créer une plateforme type __petites annonces__.  
Ce n'est pas forcément un sujet très "drôle" mais ce type d'application a 
le mérite de regrouper des fonctionnalités diverses et c'est un bon point de 
départ pour commencer avec Symfony. 

Si tu n'as pas d'idée de produit, je te proposes de créer un site
dédié à la vente et achat pour les collectionneurs de 
[canards (vivants)](https://www.youtube.com/watch?v=-w0qTvjydik).

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
    (par exemple dans le dossier dossier __www__ de __Wamp__), et tapez :  
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
    Laisse ce terminal de côté. Si tu le fermes, le serveur s'arrêtera ! Pour arrêter le serveur, appuye sur ```ctrl+c``` depuis ce terminal.

- Tapez cette url depuis votre navigateur préféré:
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
À chaque fois que tu va installer une nouvelle bibliothèque, une ligne y sera ajoutée.

Ce fichier, ainsi que les fichiers ```composer.lock``` et ```symfony.lock``` permettront aux
autres développeurs·euses participant au projet d'installer les mêmes librairies. Nous y reviendrons.

N'hésite pas à jeter un oeil et en apprendre plus sur [_Composer_](https://www.grafikart.fr/tutoriels/composer-480)
