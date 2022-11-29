# Notre première page
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).  

Pour créer [une page](https://symfony.com/doc/current/page_creation.html), Symfony a besoin de deux choses : 
- une méthode dans un contrôleur ;
- une route qui appellera ce contrôleur.

## Ton premier contrôleur
Dans le dossier __src/Controller__ crées un fichier appelé __HomeController.php__ 
qui contiendra le code suivant :
``` php
<?php

namespace App\Controller;

class HomeController
{
    public function index()
    {
        die('Home');
    }
}
```

Tu peux noter deux choses :
- le mot clé ```namespace```. (Si c'est la première fois que tu vois 
    cet animal, je te conseille __vivement__ de lire 
    [cet article sur OpenClassroom](https://openclassrooms.com/fr/courses/1217456-les-espaces-de-noms-en-php) 
    qui explique très bien cette notion). Tous les fichiers qui seront dans le dossier ```src``` auront pour 
    namespace ```App```. Ceci est défini dans le fichier ```composer.json``` 
    ``` json
    "autoload": {
            "psr-4": {
                "App\\": "src/"
            }
        },
    ```
    et répond à la 
    [PHP Standards Recommendations-4 ou PSR-4](https://www.php-fig.org/psr/psr-4/);
- le __nom du fichier__, ainsi que le __nom de la classe__, __doivent__ respecter la syntaxe 
[UpperCamelCase](https://fr.wikipedia.org/wiki/Camel_case), et ces deux noms __doivent__ être identiques.

## Crée ta première route
Il y a [plusieurs manières](https://symfony.com/doc/current/routing.html#creating-routes) 
de créer des routes dans un projet Symfony. Les deux façons les plus courantes 
sont soit en utilisant les __attributs__, soit en décrivant les routes dans le fichier ```config/routes.yaml```.

Pour cette première route, restons simple et ajoute ceci dans le fichier __config/routes.yaml__:
``` yml
home: # le nom de la route, il doit être unique et servira pour générer cette route dans un .twig ou depuis un contrôleur
    path: /home # le chemin : par exemple /user/1 sera accessible via http://nom-de-domaine.com/user/1
    controller: App\Controller\HomeController::index # namespace du controller et sa ::méthode à appeler lorsque que cette route est appelée
```
> Plus tard dans le cours, les routes seront définies en __attribut__, mais libre à toi de choisir la méthode qui te parait la plus claire.

Va voir sur [http://127.0.0.1:8000/home](http://127.0.0.1:8000/home) depuis un navigateur. Tu devrais voir s'afficher 'Home'.

Pour résumer, le chemin _/home_ (```path: /home```) appelle la méthode index (```::index```) du contrôleur (```controller: App\Controller\HomeController```).
Retiens bien ceci : un chemin appelle une méthode appartenant à un contrôleur.

## Retourner une réponse

C'est bien beau d'arrêter le script avec un ```die```, mais au final, nous voudrions renvoyer du HTML.
Pour se faire, modifie la méthode comme suit :
``` php
public function index()
{
    return('<h1>Home</h1>');
}
```
Et recharge la page sur le navigateur.  
Symfony te renvoie une erreur : 
__The controller must return a "Symfony\Component\HttpFoundation\Response" object but it returned a string ("&lt;h1&gt;Home &lt;/h1&gt;").__

Effectivement, dans Symfony il faut qu'une méthode répondant à une route retourne une réponse.  
> Et Symfony est assez clair dans son erreur. Apprends à lire les erreurs et à les interpréter, ça sera très important.  

Modifies encore un peu la méthode ```index```:
``` php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index(): Response
    {
        return new Response('<h1>Home</h1>');
    }
}
```
Note le ```use Symfony\Component\HttpFoundation\Response;``` qui permet 
d'utiliser des classes dans d'autres dossiers (voir les namespaces).  
Retournes sur [http://127.0.0.1:8000/home](http://127.0.0.1:8000/home) 
et vois le résultat.

## Les services et Twig

C'est très bien de renvoyer du HTML, mais nous souhaitons utiliser le 
pattern MVC en séparant les templates HTML.

Pour cela nous allons utiliser le moteur de template 
[__Twig__](https://twig.symfony.com/) qui est un service de Symfony.

### Un service ?
> Ton application regorge d'objets utiles : un objet "Mailer" qui peut t'aider à envoyer des e-mails tandis qu'un autre objet peut t'aider à 
enregistrer des choses dans la base de données. Presque tout ce que 
ton application « fait » est en fait réalisé par l'un de ces objets. 

> Dans Symfony, ces objets utiles sont appelés services et chaque service 
vit à l'intérieur d'un objet très spécial appelé conteneur de service. 

En somme, les services sont des objets utiles intégrés au framework, 
qu'il est possible d'utiliser au sein de nos propres classes.  
Pour en savoir plus n'hésites pas à lire [la documentation](https://symfony.com/doc/current/service_container.html).

Pour lister tous les services disponibles tape la commande suivante dans un terminal :
``` console
php bin/console debug:container
```

> Toutes les commandes concernant l'application Symfony devront se faire dans
le dossier où se trouve l'application. Tu peux remarquer que ```php bin/console``` fait appel au fichier _php_ ```console``` qui se trouve dans ```bin/```  
.  
├── bin  
│   ├── console  
N'hésites pas ouvrir ce fichier pour voir ce qu'il contient !

La liste est un peu grande... essaies de rechercher le service twig en tapant : 
``` console
php bin/console debug:container twig
# résultat
Information for Service "twig"
==============================

 Stores the Twig configuration.

 ---------------- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
  Option           Value                                                                                                                                                                                                                                                                                                                                                        
 ---------------- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
  Service ID       twig                                                                                                                                                                                                                                                                                                                                                         
  Class            Twig\Environment    
  ...
```



Pour renvoyer un template Twig tu peux utiliser ce service.

Dans __HomeController__:
``` php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment; # ajoute ce use

class HomeController
{
    
    /**
     * @var Twig\Environment
     */
    private $twig; # ajoute cette propriété et le __construct

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function index() : Response
    {
        # l'objet Environment ($this->twig) possède une méthode render
        # qui permet d'afficher un template grâce à son chemin
        return new Response($this->twig->render('home/index.html.twig'));
    }
}
```
Il faut créer le template __templates/home/index.html.twig__:
``` twig
<h1>Bienvenue sur SmallAds</h1>
```
Tu dois aussi modifier le fichier __config/services.yaml__ 
en mettant ce code à la fin :
``` yaml
services:
    
    #...

    App\Controller\HomeController:
            tags: ['controller.service_arguments']
            arguments:
                $twig: '@twig'
```
Ce fichier permet entre autres de configurer les services à appeler dans une classe.

Si tu retournes sur [http://127.0.0.1:8000/home](http://127.0.0.1:8000/home), 
tu as bien ton template qui est affiché.

## Autowiring
Tu ne vas pas injecter ce service pour chaque controller, ce serait trop ennuyeux et laborieux.

Symfony met à ta disposition [l'autowiring](https://symfony.com/doc/current/service_container/autowiring.html), c'est un système d'injection de dépendances automatique par le container, ce qui permet au framework d'injecter directement des services dans le contrôleur ou dans d'autre classe.

Pour lister les services disponibles à l'autowiring tapes la commande suivante :
``` console
php bin/console debug:autowiring
# ou 
php bin/console debug:autowiring twig
# résultat
Autowirable Types
=================

 The following classes & interfaces can be used as type-hints when autowiring:
 (only showing classes/interfaces matching twig)
 
 Stores the Twig configuration.
 Twig\Environment (twig)
```
Tu peux voir que la classe __Twig\Environment__ peut être chargée automatiquement.

Supprime ce que tu viens de faire dans __config/services.yaml__:
``` yaml
# please note that last definitions always *replace* previous ones
```
et modifie ton __HomeController__:
``` php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeController
{
    
    /**
     * @var Twig\Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index() : Response
    {
        return new Response($this->twig->render('home/index.html.twig'));
    }
}
```

Symfony a besoin de savoir que nous voulons utiliser l'autowiring dans les Controller. Pour cela, il faut éditer __config/services.yaml__:
```yaml
services:
    
    #...

    App\Controller\: #Remplace les lignes que nous avions mis à la fin
        resource: '../src/Controller/'
        tags: [ 'controller.service_arguments' ]
```

Retournes sur [http://127.0.0.1:8000/home](http://127.0.0.1:8000/home): 
Symfony sait de lui-même que tu as besoin du service Twig.

### Wut ?
![What ?](https://media.giphy.com/media/xTiIzL6KVWyWJzDXy0/giphy.gif)  

Voici en gros ce qui se passe :

Normalement, tu sais qu'il est possible (depuis php5) de 
[_typer_](https://www.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration) 
un paramètre de fonction.
``` php
<?php

function ditBonjour(string $nom) 
{
    echo 'Bonjour '. $nom;
}
# si on tente d'appeler cette fonction de la sorte
disBonjour(['toto']);
# php va lever une exception. En effet, disBonjour attend un string en paramètre
die;
``` 
> (dans certains cas, le paramètre va être "casté" (converti) en string. [Je te laisse lire ceci.](https://www.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.strict))  

Voici un autre exemple avec une classe :
``` php
<?php

class User
{
    public $nom;

    public function __construct(string $nom)
    {
        $this->nom = $nom;
    }
}


function disBonjour(User $user)
{
    echo 'Bonjour ' . $user->nom;
}

$user = new User('Anakin');
disBonjour($user); # si l'on passe autre chose qu'une instance de User, php va lever une exception. 

die;
```

En gros, Symfony utilise ce principe et va être capable de comprendre l'objet que tu demandes en paramètre de fonction ; il va se charger lui-même d'injecter cet objet.
``` php
public function __construct(Environment $twig){}
# Symfony va comprendre que __construct a besoin d'un objet de type Twig\Environment.
```
Le principe d'injection de dépendance est un pilier dans une application Symfony, nous n'avons pas fini de l'utiliser. N'hésite pas à lire [cet article](https://putaindecode.io/articles/injection-de-dependances-en-php/) qui explique ce que c'est.

# Exercices:

## Exo 1
- Crée un controller __AnnonceController__ ;
- avec une méthode __index__ ;
- qui retourne un template destiné à lister les petites annonces (pas la peine de créer le contenu du template, on verra plus tard).

> Merci d'essayer de faire l'exercice avant de regarder la correction :)

---
---
---
![Later](https://i.ytimg.com/vi/wiHYx9NX4DM/maxresdefault.jpg)  

---
---
---
### Corrections:
Dans un terminal :
``` console
php bin/console make:controller AnnonceController
```
Un controller et un template sont créés ! 

__src/Controller/AnnonceController__:
en PHP 7
```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonces", name="app_annonce")
     */
    public function index()
    {
        return $this->render('annonce/index.html.twig');
    }
}
```
En PHP 8
```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', name: 'app_annonce')]
    public function index(): Response
    {
        return $this->render('annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }
}
```
> J'ai un peu triché j'avoue. Tu peux voir toutes les lignes de commande Symfony en tapant ```php bin/console``` et accéder à des pouvoirs illimités !

Tu peux voire que la définition des routes n'est pas pareille en PHP 7 et en PHP 8. En PHP, il existe ce qu'on appelle des annotations : ce sont des commentaires qui permettent d'ajouter des informations, des meta-data à notre code. Elles ont la forme suivante :
```php
    /**
     * @param string $test
     * @return void
     */
    public function quiNeFaitRien($test) {

    }
```
Mais on peut Type Hinter les variables me direz-vous ! Oui, mais dans les versions précédentes de PHP, cette fonctionnalité n'était pas présente !  
Symfony utilisait ces annotations pour lire les routes. Depuis PHP 8, nous pouvons utiliser les __attributs__.
 

![Pouvoir illimités](https://media.giphy.com/media/3o84sq21TxDH6PyYms/giphy.gif)

Tu peux noter que le controller hérite de __AbstractController__. Cela permet à la classe fille d'utiliser certains services du container sans avoir à faire toi-même l'injection de dépendance, et que la route n'est pas en yaml mais en __annotation__ ou __attribut__ pour PHP 8.

## Exo 2
- Fais hériter __HomeController__ de __AbstractController__ ;
- profite des méthodes de __AbstractController__ pour renvoyer le template twig plus facilement ;
- et utilise les annotations à la place du yaml pour faire correspondre la méthode ```index``` au chemin ```/```.

Notes qu'en décrivant les routes en annotation, il ne faut pas oublier le ```use Symfony\Component\Routing\Annotation\Route;``` et que l'annotation doit être juste au-dessus de la fonction qui doit être appelée par la route.

> Merci d'essayer de faire l'exercice avant de regarder la correction :)

---
---
---
![Later](https://i.ytimg.com/vi/p1TzpYtcyfA/maxresdefault.jpg)  

---
---
---

### Correction
__config/routes.yaml__
``` yaml
#home:
#    path: /home
#    controller: App\Controller\HomeController::index
```

__src/Controller/HomeController__:

``` php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index()
    {
        return $this->render('home/index.html.twig');
    }
}
```

Tu remarques qu'on peut choisir le nom de routes, cela veut dire qu'il va falloir rester cohérent dans les nommages de nos routes
afin d'avoir toujours la même convention et de ne pas se perdre. On pourrait choisir une convention tel que ````annonce.index```` par exemple.
Mais d'expérience, nous allons vite arriver à faire des fautes de syntaxe, ou à oublier, voir à changer notre convention en cours de route.
Le plus simple est donc de laisser faire Symfony.

Dans tes contrôleurs, je te propose de supprimer le nom des routes, afin de laisser Symfony les gérer tout seul :
``` php
# AnnonceController
 #[Route('/annonce')]
 public function index()
 {
     //...
 }

# HomeController
  #[Route('/')]
  public function index()
  {
      //...
  }
```

Tu peux lister les routes de l'application en tapant ```php bin/console debug:router``` dans un terminal. Cette commande servira plus tard ;)

Rendez-vous dans la prochaine section pour la suite !  
![next](https://media.giphy.com/media/RIkgfue3mXysO7Gw5h/giphy.gif)
