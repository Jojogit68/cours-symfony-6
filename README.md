# Security suite
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

Tu as des utilisateurs et des annonces dans ton application. Super !
Maintenant il serait intéressant de lier les annonces aux utilisateurs, n'est-ce pas ?

## Relation User et Annonce
Ce que l'on souhaite maintenant, c'est qu'un utilisateur puisse créer, modifier, supprimer... ses annonces.
Pour cela, il va falloir faire une relation en base de données, avec les fameuses Foreign Key.

Les notions de bases de données relationnelles sont toujours les mêmes.
Ce qui change, c'est que tu ne vas pas créer les relations à la main dans la base de données, mais c'est Doctrine qui va s'occuper de tout !
Donc comme avant :
> Pas de manipulation à faire dans phpMyAdmin ou autre !  
![don't make me destroy you](https://media.giphy.com/media/LQx7NoYAJIozS/giphy.gif)

### Petit rappel sur les relations
Si les mots OneToMany, OneToOne ou encore ManyToMany te sont inconnus, je te propose de lire ce [cours sur OpenClassroom](https://openclassrooms.com/fr/courses/4055451-modelisez-et-implementez-une-base-de-donnees-relationnelle-avec-uml/4457718-mettez-en-oeuvre-les-differents-types-de-relations-a-laide-des-cles-etrangeres).

#### Notion de propriétaire et d'inverse :
La notion de propriétaire et d'inverse est abstraite, mais importante à comprendre.
Dans une relation entre deux entités, il y a toujours une entité dite propriétaire, et une dite inverse.
L'entité propriétaire et l'entité qui est responsable de la relation.
[Voir la documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association).

Ici la relation propriétaire sera l'entité Annonce puisque c'est elle qui contiendra la liaison vers les utilisateurs (grâce aux ID)

### Création de la relation
Ajoutons cette relation grâce à la commande qui suit et lis bien la sortie de la console avant de taper les commandes à l'aveugle :
```shell
php bin/console make:entity Annonce

Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > user # le nom de la propriété, rien ne change de d'habitude

 Field type (enter ? to see all types) [string]:
 > relation # ici tu peux taper ? pour voir tous les champs disponibles. Quand tu tape relation, Symfony va t'aider à choisir la relation nécessaire entre tes entités

 What class should this entity be related to?:
 > User # le nom de l'entité qui doit être lié à Annonce

What type of relationship is this?
 ------------ ------------------------------------------------------------------- 
  Type         Description                                                        
 ------------ ------------------------------------------------------------------- 
  ManyToOne    Each Annonce relates to (has) one User.                            
               Each User can relate to (can have) many Annonce objects            
                                                                                  
  OneToMany    Each Annonce can relate to (can have) many User objects.           
               Each User relates to (has) one Annonce                             
                                                                                  
  ManyToMany   Each Annonce can relate to (can have) many User objects.           
               Each User can also relate to (can also have) many Annonce objects  
                                                                                  
  OneToOne     Each Annonce relates to (has) exactly one User.                    
               Each User also relates to (has) exactly one Annonce.               
 ------------ ------------------------------------------------------------------- 

 Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
 > ManyToOne # en lisant les description plus haut, il est facile de choisir la relation adequate

 Is the Annonce.user property allowed to be null (nullable)? (yes/no) [yes]:
 > no # une annonce doit obligatoirement appartenir à un utilisateur, sinon ça n'a pas de sens

 Do you want to add a new property to User so that you can access/update Annonce objects from it - e.g. $user->getAnnonces()? (yes/no) [yes]:
 > yes

 A new property will also be added to the User class so that you can access the related Annonce objects from it.

 New field name inside User [annonces]:
 > annonces

 Do you want to activate orphanRemoval on your relationship?
 A Annonce is "orphaned" when it is removed from its related User.
 e.g. $user->removeAnnonce($annonce) 
 NOTE: If a Annonce may *change* from one User to another, answer "no".

 Do you want to automatically delete orphaned App\Entity\Annonce objects (orphanRemoval)? (yes/no) [no]:
 > yes # lors de la suppression d'un utilisateur, est ce que ses annonces doivent aussi être supprimées ? 

 updated: src/Entity/Annonce.php
 updated: src/Entity/User.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > #[tape entrée]


           
  Success! 
           

 Next: When you're ready, create a migration with make:migration

```
Et procède à la mise à jour de la base de données :
```shell
php bin/console make:migration
php bin/console doctrine:migration:migrate
```

ARF !
```shell
[notice] Migrating up to DoctrineMigrations\Version20221211164244
[error] Migration DoctrineMigrations\Version20221211164244 failed during Execution. Error: "An exception occurred while executing a query: SQLSTAT
E[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`main`.`#sql-1_25`, CONSTRAINT `FK_F65593E5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`))"

In ExceptionConverter.php line 56:
                                                                                                                                                  
  An exception occurred while executing a query: SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a fore   
  ONSTRAINT `FK_F65593E5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`))                                                               
                                                                                                                                                  

In Connection.php line 69:
                                                                                                                                                  
  SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`main`.`#sql-1_25`, C   
  ONSTRAINT `FK_F65593E5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`))                                                         
```

Nous avons une belle erreur ! C'est normal, nous essayons d'ajouter une colonne avec un ID, et cet ID est une __foreign key__ !
Sa valeur ne peut donc pas être ```null```, ni 0, ni un ID qui n'existe pas en base de données ! Et c'est ce que nous essayons de faire en lançant la migration : mettre une valeur invalide.

Pour résoudre le problème rapidement et parce que nous sommes en phase de conception et de développement, nous pouvons tout simplement :
- supprimer la base de données ;
- la re-créer ;
- créer les tables et leurs champs ;

Et tu sais quoi ? Tu connais presque toutes les commandes à exécuter :
```shell
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Les annonces sont maintenant liées aux utilisateurs. Tu peux aller voir en base de données pour t'en assurer.

## Fixtures
### Exercice
Dans les fixtures, nous allons pouvoir lier des annonces aux utilisateurs afin d'avoir des données de test.

Si tu regardes dans ton entité __Annonce__, il y a une nouvelle fonction ```setUser``` qui permet d'assigner un User à l'annonce.
Tu vas pourvoir l'utiliser de cette manière :
```php
$annonce = new Annonce();
$annonce->setUser($user);
```

Dans les fixtures __AnnonceFixtures__, tu pourras assigner un __User__ de façon aléatoire à chaque annonce.  
Il va simplement falloir charger les fixtures des Users __avant__ les fixtures Annonces.
Pour faire cela, tu peux te référer à la [documentation](https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html#loading-the-fixture-files-in-order).

Je te laisse essayer.
![Later](https://i.ytimg.com/vi/Uqn7V-Fjjq0/maxresdefault.jpg)
#### Correction

```php
<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AnnonceFixtures extends Fixture implements DependentFixtureInterface
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        $users = $this->userRepository->findAll();
        $usersLength = count($users)-1;
        for ($i=0; $i < 1000; $i++) {
            // permet d'avoir un utilisateur random
            // possible à faire avec Faker mais plus lourd en ressource
            $randomKey = rand(0, $usersLength);
            $user = $users[$randomKey];
            $annonce = new Annonce();
            $annonce
                ->setTitle($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setPrice($faker->numberBetween(10, 100))
                ->setStatus($faker->numberBetween(0, 4))
                ->setIsSold(false)
                ->setUser($user)
            ;
            $manager->persist($annonce);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
```

## Section Profil
Je te propose de créer une nouvelle section dédiée aux utilisateurs de l'application disposant d'un compte.

Lors de sa connexion à l'application, l'utilisateur aura accès à une section profil dans laquelle il pourra entre autre :
- lister __ses__ annonces ;
- modifier __ses__ annonces ;
- supprimer __ses__ annonces ;
- créer une annonce ;
- et tout ce que tu jugeras utile.

### ProfilController
À la manière du contrôleur ```App\Controller\Admin\AnnonceController```, nous allons créer un contrôleur ```App\Controller\Profil\AnnonceController```.
```shell
php bin/console make:controller Profile\\Annonce
```
#### Récupérer l'utilisateur
Pour [récupérer l'utilisateur en session](https://symfony.com/doc/current/security.html#a-fetching-the-user-object) depuis un controller, c'est très simple.   
Dans la méthode ```index``` du contrôleur que tu viens de créer, tu peux ajouter ```dump($this->getUser());``` et voir le résultat du ```dump``` dans la debug bar en allant sur __/profile/annonce__.


```php
<?php
# src/Controller/Profile/AnnonceController.php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/profile/annonce', name: 'app_profile_annonce')]
    public function index(): Response
    {
        dump($this->getUser()); // voici comment récupérer un utilisateur en session depuis un contrôleur
        return $this->render('profile/annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }
}

```
Essaye d'afficher le résultat du ```dump``` en étant déconnecté·e, puis connecté·e à l'application (avec un utilisateur que tu as créé dans les fixtures par exemple).

#### Lister les annonces
Grâce à la relation entre les utilisateurs et les annonces, il est facile de récupérer les annonces d'un utilisateur.
Regardes dans ton entité __User__, il devrait y avoir une méthode qui se nomme ```getAnnonces``` qui comme son nom l'indique permet de...  
![drumroll](https://media.giphy.com/media/116seTvbXx07F6/giphy.gif)

Dans ton controller, tu peux donc facilement récupérer toutes les annonces d'un utilisateur en procédant de la sorte :
```php
public function index()
{
    return $this->render('profile/index.html.twig', [
        'annonces' => $user->getAnnonces(), // récupère toutes les annonces de l'utilisateur
    ]);
}
```

Et tu peux mettre à jour le template __templates/profile/annonce/index.html.twig__ :
```php
{% extends 'base.html.twig' %}

{% block title %}Hello {{ app.user.getFirstName }}!{% endblock %}
{% block body %}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>
<div class="row">
    <a href="#" class="btn btn-primary">Créer une annonce</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {% for annonce in annonces %}
            <tr>
                <td>
                    {{ annonce.title }}
                </td>
                <td>
                    <a href="#" class="btn btn-secondary">Éditer</a>
                    <form method="post" action="#" onsubmit="return confirm('Êtes vous vraiment sûr ?')">
                        <input name="_method" type="hidden" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ annonce.id) }}">
                        <button class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            {% endfor %}
        </tbody>
    </table>
</div>
{% endblock %}
```

## Les rôles

La notion de rôle et autorisations est très simple :  
pour limiter l'accès à certaines pages, on va se baser sur les rôles de l'utilisateur.
Ainsi, limiter l'accès au panel d'administration revient à limiter cet accès aux utilisateurs disposant du rôle ```ROLE_ADMIN``` (par exemple).

Lorsqu'un utilisateur se connecte et qu'il essaie d'accéder à une page, Symfony appelle la méthode ```getRoles()``` sur l'objet User
et vérifie si l'utilisateur possède le rôle suffisant.
Dans la classe User que nous avons générée précédemment, les rôles sont un tableau stocké dans la base de données
et chaque utilisateur se voit toujours attribuer au moins un rôle : ```ROLE_USER``` :

```php
// src/Entity/User.php

// ...
class User
{
    #[ORM\Column]
    private array $roles = [];

    // ...
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
}
```

Voici quelques directives pour définir les rôles :
- Chaque rôle doit commencer par ```ROLE_``` (sinon, le système ne fonctionne pas comme prévu) ;
- Un rôle est une chaîne de caractère arbitraire que tu peux inventer selon tes besoins (par exemple ```ROLE_REDACTOR```).
- Tous les utilisateurs connectés doivent avoir le rôle ```ROLE_USER```, il ne faut donc surtout pas supprimer la ligne ```$roles[] = 'ROLE_USER';```
  Il peut y avoir plusieurs manières de définir une stratégie d'autorisation avec des rôles :
- créer un rôle par action. Par exemple ```ROLE_CREATE_ANNONCE```, ```ROLE_EDIT_ANNONCE```, ```ROLE_VIEW_ANNONCE```, etc... On peut imaginer créer des profils qui auront plusieurs de ces roles et qui seront liés à des utilisateurs ;
- créer des rôles généraux, qui permettront d'exécuter plusieurs actions. Par exemple ```ROLE_ADMIN``` permet d'administrer un site, ```ROLE_AUTHOR``` permet de gérer des articles, etc... ;
- autres...

Tu peux lire [cette section](Pour en savoir plus: https://symfony.com/doc/current/security.html#roles) pour en apprendre d'avantage !

Nous allons rester simple et utiliser ce que Symfony nous propose et qui fonctionne très bien.

### Rôles hiérarchiques
Pour donner plusieurs rôles à chaque utilisateur, tu peux définir des règles d'héritage de rôle en créant une hiérarchie de rôles :
```yaml
# config/packages/security.yaml
security:
    # ...

    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
```
Ainsi, les utilisateurs avec le rôle ```ROLE_ADMIN``` auront également le rôle ```ROLE_USER```. Et les utilisateurs avec ```ROLE_SUPER_ADMIN``` auront automatiquement ```ROLE_ADMIN```, ```ROLE_ALLOWED_TO_SWITCH``` et ```ROLE_USER``` (hérité de ```ROLE_ADMIN```).

### Restreindre une section
Il est facilement possible de restreindre l'accès à une section entière de l'application.
Dans le fichier __config/packages/security.yaml__ qui permet de configurer le composant __Security__, tu peux juste ajouter une règle __access_control__ :
```yaml
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN } # pour aller sur une URL qui commende par /admin, l'utilisateur devra avoir le role ROLE_ADMIN
        - { path: ^/profile, roles: ROLE_USER }
```

Désormais, un utilisateur qui veut aller sur une route commençant par __/profile__ doit avoir le rôle ```ROLE_USER```.
De même, un utilisateur qui désire aller sur commençant par __/admin__ doit avoir le rôle ```ROLE_ADMIN```.

## Vérifier les rôles
Avant de passer à la pratique, voici quelques méthodes permettant de vérifier les rôles.

### Dans les controller
Pour rediriger un utilisateur qui n'a pas le rôle ```ROLE_ADMIN``` vers la page de login, il suffit d'écrire le code suivant depuis une méthode de contrôleur :
```php
public function adminDashboard()
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    // or add an optional message - seen by developers
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
}
```

Ou tu peux aussi utiliser les attributs :
```php
// src/Controller/AdminController.php
// ...

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 */
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Require ROLE_ADMIN for only this controller method.
     */
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard()
    {
        // ...
    }
}
```

Enfin pour vérifier dans une méthode d'un controller si l'utilisateur possède un rôle, il suffit d'écrire :
```php
$hasAccess = $this->isGranted('ROLE_ADMIN');
if ($hasAccess) {
    // telle action si l'utilisateur a ROLE_ADMIN
}
```

Attention à ne pas vérifier les rôles en écrivant :
```php
$hasAccess = in_array('ROLE_ADMIN', $user->getRoles());
```
Cela fonctionnerait, mais ne prendrait pas en compte la hiérarchie des rôles !

Pour vérifier si une annonce appartient bien à l'utilisateur connecté dans un contrôleur, tu peux écrire une condition qui ressemble à celle-ci:
```php
public function edit(Annonce $annonce, EntityManagerInterface $em, Request $request)
{
    if ($this->isGranted('ROLE_USER') !== null && $annonce->getUser() !== $this->getUser()) {
        $this->addFlash('warning', 'Vous ne pouvez pas accéder à cette ressource');
        return $this->redirectToRoute('profile');
    }
}
```

Tu peux aussi le vérifier en utilisant les attributs :
```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
// reste du code
    #[Security("is_granted('ROLE_USER') and annonce.getUser() == user")]
    public function edit(Annonce $annonce, EntityManagerInterface $em, Request $request)
    {
        //...
    }
```

Pour en savoir plus sur ```@Security```, tu peux te référer à la [documentation](https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html).

Tu peux aussi utiliser les [voters](https://symfony.com/doc/current/security/voters.html), mais c'est une fonctionnalité un peu plus avancée.
Je te laisse lire la documentation à ce sujet.

### Dans Twig

Tu peux aussi afficher ou non un élément dans les templates twig en écrivant :
```php
{% if is_granted('ROLE_ADMIN') %}
    <a href="...">Delete</a>
{% endif %}
``` 

### Conditions dans les formulaires
Admettons que nous souhaitions que seuls les utilisateurs disposant du ```ROLE_ADMIN``` puissent changer le slug d'une annonce ? Il faudrait afficher les champs du formulaire seulement si l'utilisateur connecté possède ce rôle.

Pour vérifier si l'utilisateur connecté possède le ```ROLE_ADMIN``` dans les FormType, tu vas devoir injecter le service ```AuthorizationCheckerInterface``` qui permet de vérifier si l'utilisateur possède un rôle ou non.

Comment je sais ça ???
J'ai lu la [documentation](https://symfony.com/doc/current/components/security/authorization.html), et j'ai regardé ce que fait ```$this->isGranted()``` dans un contrôleur.

Cette fonction va chercher le service ```$this->container->get('security.authorization_checker')``` que l'on peut trouver en cherchant dans le container avec ```php bin/console debug:container authorization_checker```.

Il ne reste plus qu'à injecter ce service dans le FormType.

Voici comment tu peux procéder :
```php
# src/Form/AnnonceType.php
<?php

//...
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AnnonceType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
           //...
        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('slug', null, ['label' => 'slug'])
            ;
        }
    }
}

```

### Exercices

Sécurise les actions permettant d'ajouter, modifier, supprimer une annonce :
- un utilisateur doit être connecté pour créer un annonce (```ROLE_USER```) ;
- un utilisateur ne peut modifier et supprimer que __ses__ annonces ;
- seul un admin (```ROLE_ADMIN```) a le droit de modifier et supprimer des annonces qui ne lui appartiennent pas.

Dans le formulaire permettant de créer une annonce, seuls les utilisateurs possédant le ```ROLE_ADMIN``` peuvent afficher les champs date de création et slug.

Dans les templates Twig :
- le lien de création d'une annonce ne doit pas être affiché si l'utilisateur ne possède pas le rôle ```ROLE_USER``` ;
- les boutons permettant de supprimer ou modifier une annonce ne doivent être affiché que pour les admins et les propriétaires des annonces en question.

---
---
---
![Later](https://static.boredpanda.com/blog/wp-content/uploads/2019/11/20-5dce8cedac70f__700.jpg)

---
---

#### Correction

```php
# src/Controller/AnnonceController.php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
//...

    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        //...
    }

    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and annonce.getUser() == user)")]
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        //...
    }

    #[Route('/annonce/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and annonce.getUser() == user)")]
    public function delete(Annonce $annonce, EntityManagerInterface $em, Request $request): RedirectResponse
    {
        //...
    }
```

```php
#src/Form/AnnonceType.php
//...
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
//...
class AnnonceType extends AbstractType
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }
    
    //... 
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'title'])
            //...
        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('createdAt', DateType::class, [
                    'label' => 'created_at',
                    'widget' => 'single_text',
                    'input'  => 'datetime_immutable'
                ])
                ->add('slug', null, ['label' => 'slug'])
            ;
        }
    }
    //...
}
```

```php
{# templates/_inc/sidebar-nav.html.twig #}
<ul class="nav flex-column">
    <li class="nav-item">
        {% if is_granted('ROLE_USER') %}
            <a href="{{ path('app_annonce_new') }}" class="nav-link">Créer une annonce</a>
        {% endif %}
    </li>
</ul>
```

```php
{# templates/annonce/_card.html.twig #}
{# ... #}
{% if is_granted('ROLE_USER') and annonce.user == app.user %}
    <a class="btn btn-sm btn-outline-secondary" type="button" href="{{ path('app_annonce_edit', {id: annonce.id}) }}">Edit</a>
    <form method="post" action="{{ path('app_annonce_delete', {id: annonce.id}) }}" onsubmit="return confirm('Êtes vous vraiment sûr ?')">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ annonce.id) }}">
        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
    </form>
{% endif %}
```

Bravo, nous avons sécurisé notre application !  
![bravo](https://media.giphy.com/media/cbb8zL5wbNnfq/giphy.gif)
