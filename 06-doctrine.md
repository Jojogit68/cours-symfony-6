# Doctrine ORM
> Les documentations seront tes meilleures amies si tu souhaite progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).  

[Doctrine](https://www.doctrine-project.org/) est un [ORM](https://fr.wikipedia.org/wiki/Mapping_objet-relationnel) et il vient par défaut avec Symfony.

L'objectif d'un ORM (pour Object-Relation Mapper) est simple : se charger de l'enregistrement de tes données en te faisant oublier que tu as une base de données. Tu ne vas plus écrire de requêtes, ni créer de tables via phpMyAdmin ! Dans ton code PHP, tu vas faire appel à Doctrine, l'ORM par défaut de Symfony.

## Utilises des objets et non des requêtes
Tu disposes, par exemple, d'une variable ```$annonce``` contenant un objet ```Annonce```, qui représente une annonce dans notre application.  
Habituellement, pour sauvegarder cet objet, tu ferais : ```INSERT INTO table annonce VALUES ('valeur 1', 'valeur 2', ...)```.
En utilisant un ORM, tu feras ```$orm->save($annonce)```, et ce dernier s'occupera de tout.

## Tes données sont des objets
Retiens bien cela : toutes tes données doivent être sous forme d'objet. Tu ne feras donc plus ```$annonce['title']```, mais ```$annonce->getTitle()```.  
La puissance d'un ORM ne se résume pas qu'à cela, tu pourras utiliser des méthodes du type : ```$user->getAnnonce()```, ce qui déclencherait la bonne requête.

Vocabulaire : 
- un objet dont tu confies l'enregistrement à l'ORM s'appelle une __entité__ (entity en anglais) ;
- on dit également __persister__ une entité, plutôt qu'enregistrer une entité.

> Je ne veux pas te voir faire des manipulations dans phpMyAdmin ! Pas de création de base, de création de table ou que sais-je encore ! C'est Doctrine qui va s'occuper de tout ! ![don't make me destroy you](https://media.giphy.com/media/LQx7NoYAJIozS/giphy.gif)

## Configure ta base de données
La première chose à faire est de dire à Symfony comment se connecter à la base de données. 
Pour configurer l'accès à la base de données, ou l'accès à une API, ou encore à un serveur SMTP, Symfony met à disposition un fichier qui nous va nous permettre de renseigner 
des identifiants. C'est le fichier __.env__ à la racine que je t'invite à ouvrir et à lire.  
Ce fichier est ici pour l'exemple ! Il ne faut pas écrire d'identifiants dedans, car il est voué à être versionné. C'est-à-dire qu'il sera visible sur le dépôt GIT.
Et on ne souhaite pas que quelqu'un ait accès à des informations sensibles, comme un mot de passe de base de données par exemple !

Nous pouvons donc créer un fichier __.env.local__ qui ne sera pas versionné et qui sera spécifique à chaque environnement (dev en local, preprod, prod, etc...).
Symfony scannera d'abord ce fichier avant de scanner le fichier __.env__ s'il ne trouve pas l'information dont il a besoin.

Crée un fichier __.env.local__ et ajoute la ligne suivante, en changeant les informations par les identifiants de connexions à ta base de données :
```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```
Dans un terminal:
```shell
php bin/console doctrine:database:create
```
Ta base de données vient d'être créée par Doctrine (tu peux aller vérifier dans phpMyAdmin, mais juste vérifier !).

## Le modèle Annonce
Pour dialoguer avec la base de données, Doctrine a besoin de modèles, qu'on appelle __Entity__ dans Symfony.  
Les entités sont des classes PHP qui vont décrire notre schéma de base de données.
Par exemple une classe ```class User```, correspondra à la table ```user``` en base de données.
Cette classe aura des propriétés qui correspondront à des champs en base de données, si bien 
qu'une propriété ```private string $name``` par exemple, aura un champ ```name``` en base de données.

```php
<?php

class User // nom de la table
{
    private string $name; // nom du champ
}
```
> Il faut donc retenir que pour créer nos tables et pour dialoguer avec la BDD, nous allons créer des entités.

Pour créer une nouvelle entité, il faut taper la commande suivante :
```shell
php bin/console make:entity
```
Le console te demande le nom de l'entité et ses champs, il suffit de répondre comme ci-dessous :
```shell
 Class name of the entity to create or update (e.g. VictoriousElephant):
 > Annonce

 created: src/Entity/Annonce.php
 created: src/Repository/AnnonceRepository.php
 
 Entity generated! Now lets add some fields!
 You can always add more fields later manually or by re-running this command.

 New property name (press <return> to stop adding fields):
 > title

 Field type (enter ? to see all types) [string]:
 > string

 Field length [255]:
 > 

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > description

 Field type (enter ? to see all types) [string]:
 > text

 Can this field be null in the database (nullable) (yes/no) [no]:
 > yes

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > # appuie sur Entrée pour terminer


           
  Success! 
           

 Next: When you re ready, create a migration with make:migration
```
Symfony à généré deux nouveaux fichiers :
- __src/Entity/Annonce.php__ correspond à l'entité, et par extension à la table en BDD
- __src/Repository/AnnonceRepository.php__ permet de dialoguer avec la base de données. C'est dans ces fichiers Repository, que tu écriras tes requêtes à la base de données.

Ouvre le fichier __src/Entity/Annonce.php_. Voici ce qu'il contient: 
```php
<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM; // On utilise la classe Doctrine\ORM\Mapping et l'appelle ORM. Cette classe nous permet de décrire les champs en base de données


#[ORM\Entity(repositoryClass: AnnonceRepository::class)] # on utilise la classe ORM avec les attributs, pour spécifier que Annonce est une entité et son Repository (pour dialoguer avec la BDD) 
class Annonce # le nom de la lasse correspond à la table en BDD, en minuscule: Annonce > annonce
{
    #[ORM\Id] # cette propriété sera l'identifiant de l'entité
    #[ORM\GeneratedValue] # elle sera auto-généré
    #[ORM\Column] # c'est un champ (une colonne) dans la table annonce
    private ?int $id = null;

    #[ORM\Column(length: 255)] # cette propriété fait 255 caractères
    private ?string $title = null; # c'est une chaine de caractères, donc un VARCHAR en BDD

    #[ORM\Column(type: Types::TEXT, nullable: true)] # type TEXT en BDD, peut être null
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null; # type int, donc INT en BDD

    // les getter et setter permettant l'encapsulation et la protection des proprités
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this; // les setters retourne l'instance de la classe afin que l'on puisse chainer les méthodes. Ce sont des fluent setters
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
```
Tu peux remarquer que toutes les propriétés de l'entité sont privées, c'est pour éviter que l'on mette n'importe quoi dedans et pour avoir plus de contrôle sur les valeurs. 
Les entités ont donc besoin de __getter__ et de __setter__ pour accéder ou modifier les propriétés.

Pour le moment rien n'a été ajouté en BDD. En effet, créer ou même modifier une entité ne modifie pas la base de données directement. 
Pour cela, nous devons créer une __migration__. Les migrations sont des fichiers dont le rôle est de mettre à jour la base de données.

Pour créer une migration, il faut lancer la commande suivante :
```shell
php bin/console make:migration
```
Symfony te recommande d'aller voir le fichier généré. 
```shell
Next: Review the new migration "src/Migrations/Version20190220073837.php"
```
Je te propose d'ouvrir ce fichier et de lire son contenu avant de lancer la commande suivante.
Il faut toujours vérifier le contenu des migrations, afin de s'assurer que leur contenu correspond bien à ce que tu veux faire.

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221129170255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create annonce table and messenger_messages tables';
    }

    public function up(Schema $schema): void
    {
        // c'est cette fonction qui sera lancée pour créer la table annonce 
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // on peut toujours revenir en arrière grâce à cette fonction
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

```
Il ne s'est toujours rien passé en BDD ! La commande précédente permet juste de créer un fichier de migration. Pour ce faire
elle scan la base de données et les entités. Si une entité à été ajouté, si des champs ont été ajouté / supprimé dans les entité, etc... 
la commande génère une migration en conséquence. Ce sont les entités qui sont la référence, et non la base de données. Il faut donc 
toujours passer par les entités pour modifier la base de donées.

Si c'est OK, tu peux lancer la commande suivante qui va lancer les migrations qui n'ont pas encore été appliqué :
```shell
php bin/console doctrine:migrations:migrate

WARNING! You are about to execute a migration in database "main" that could result in schema changes and data loss. 
Are you sure you wish to continue? (yes/no) [yes]:
> # en appuyant sur la touche entrée, c'est la valeur contenue entre crochets qui sera choisit, donc ici [yes]  
```
Tu as une nouvelle table __annonce__ qui correspond à ton entité ainsi qu'une table __migration_versions__ qui correspond aux différentes migrations (il ne faut pas toucher à cette table sauf pour des cas spécifiques).

Les migrations te seront très utiles pour mettre à jour la base de données en production. C'est l'historique de ta base de données !

Pour voir le status des migrations, tu peux lancer la commande 
```shell
php bin/console doctrine:migrations:status
```
> Ah oui ! Toutes les commandes peuvent être raccourcies grâce aux premières lettres de chaque mot. Ainsi ```php bin/console doctrine:migration:migrate``` peut s'écrire aussi ```php bin/console d:m:m```
> et tu peux voir la liste des commandes en utililsant 
```shell
php bin/console list
# puis 
php bin/console list doctrine 
# puis 
php bin/console list doctrine:migrations
# puis 
php bin/console list doctrine:migrations:status --help  # pour avoir de l'aide
```
## Ajoute des propriétés
Nous allons ajouter quelques champs à l'entité Annonce. On se souvient : on ne touche pas directement à la base de données !!
On passe toujours pas les entités !  
Pour ajouter des propriétés à ton entité, tu peux à nouveau passer par la console:
```shell
php bin/console make:entity Annonce
```
Et entre les réponses suivantes :
```shell
Your entity already exists! So lets add some new fields!

 New property name (press <return> to stop adding fields):
 > price

 Field type (enter ? to see all types) [string]:
 > integer

 Can this field be null in the database (nullable) (yes/no) [no]:
 > # appuie sur la touche entrée, la valeur [no] est choisit

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > # entrée pour arrêter


           
  Success! 
           

 Next: When you're ready, create a migration with make:migration
```

Lance ensuite la commande suivante :
```shell
php bin/console m:mi # php bin/console make:migration pour créer un fichier de migration
php bin/console d:m:m # php bin/console doctrine:migrations:migrate pour lancer la migration
```
### Exercices  

Grâce à la console ! Ajoute les propriétés suivantes à l'entité __Annonce __:
- status / integer / nullable:no
- isSold / boolean / nullable:no
- createdAt / datetime_immutable / nullable:no
Avant de créer une migration, modifie le fichier __src/Entity/Annonce__ comme ci dessous:

```php
<?php
// reste du code...
class Annonce
{
    // ces constantes nous permettront d'ajouter un status aux annonces
    const STATUS_VERY_BAD  = 0;
    const STATUS_BAD       = 1;
    const STATUS_GOOD      = 2;
    const STATUS_VERY_GOOD = 3;
    const STATUS_PERFECT   = 4;

    // ... 
    
    // le champ is_sold sera à 0 par défaut en BDD
    #[ORM\Column(options: ['default' => false])]
    private ?bool $isSold = false;

    // ...
    
    public function setStatus(int $status): self
    {
        $allowedStatus = [
            self::STATUS_VERY_BAD,
            self::STATUS_BAD,
            self::STATUS_VERY_GOOD,
            self::STATUS_VERY_GOOD,
            self::STATUS_PERFECT
        ];
        // si le status passé en argument ne correspond à aucun status, on lève une erreur
        // voici un bon exemple d'utilisation de setter permettant de contrôler les données
        // si la propriété avait été public, on aurait pu y mettre n'importe quoi et avoir un status... avec la valeur 50 par exemple
        if (!in_array($status, $allowedStatus)) {
            throw new \InvalidArgumentException('Invalid status');
        }
        
        $this->status = $status;

        return $this;
    }
```
- crée une migration 
- mets à jour la base de données :)

## Créer une annonce
L'objectif ici est de créer une annonce en passant par la programmation. Le but sera d'ajouter une annonce via un formulaire, et Symfony possède son propre système de formulaire.
Vu qu'on ne sait pas encore utiliser les formulaires Symfony, on va voir comment faire un ```INSERT INTO``` mais façon Symfony/Doctine.

Voici comment __hydrater__ et sauvegarder un nouvel objet de type __Annonce__ en base de données

Dans __AnnonceController__, ajoute cette nouvelle fonction : 

```php
use App\Entity\Annonce; // ne pas oublier ce use !
//...

    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        $annonce = new Annonce();
        $annonce
            ->setTitle('Ma collection de canard')
            ->setDescription('Vends car plus d\'utilité')
            ->setPrice(10)
            ->setStatus(Annonce::STATUS_BAD)
            ->setIsSold(false)
            ->setCreatedAt(new \DateTimeImmutable())
        ;
        
        dump($annonce);
        die;
        
        // dd($annonce); permet de faire la même chose que dump($annonce); die;
    }
```
Va sur la page pour créer une nouvelle annonce, tu peux voir qu'un objet annonce est créé et __hydraté__ avec les données que nous lui avons passées.

Pour enregistrer l'annonce en base de données, il faut dire à Doctrine 
- de persister l'objet (cet objet doit être enregistré en base de données) ;
- de flush (envoie les objets persistés en base de données).

Voici ce que cela donne dans ton code:
```php
use Doctrine\Persistence\ManagerRegistry; // ne pas oublier ce use
//...
    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine): Response
    {
        $annonce = new Annonce();
        $annonce
            ->setTitle('Ma collection de canards')
            ->setDescription('Vends car plus d\'utilité')
            ->setPrice(10)
            ->setStatus(Annonce::STATUS_BAD)
            ->setIsSold(false)
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        // On récupère l'EntityManager
        $em = $doctrine->getManager();
        // On « persiste » l'entité
        $em->persist($annonce);
        // On envoie tout ce qui a été persisté avant en base de données
        $em->flush();

        return new Response('annonce bien créée');
    }
```

## Récupère l'annonce
Pour récupérer un objet depuis la base de données, tu as besoin du repository, du _dépôt_ de l'entité. Ce sont les dépôts qui 
permettent de faire des requêtes à la base de données, et c'est dans ceux-ci que nous écrirons nos requêtes les plus complexes !

Pour récupérer une annonce, nous avons besoin du dépôt de l'entité __Annonce__, qui est tout simplement __AnnonceRepository__. 
Tu peux le récupérer de plusieurs façons :
- En utilisant le ManagerRegistry :
```php
    use Doctrine\Persistence\ManagerRegistry; // ne pas oublier ce use !
    
    //...

    class AnnonceController extends AbstractController
    {
        public function index(ManagerRegistry $doctrine)
        {
            $repository = $doctrine->getRepository(Annonce::class);
        }
```
- Encore mieux, tu peux injecter direment le Repository !
```php
    use App\Repository\AnnonceRepository; // ne pas oublier ce use !

    public function index(AnnonceRepository $annonceRepository)
    {
       //...
    }
```

Nous utiliserons l'autowiring du repository dans une méthode. Ensuite nous pourrons utiliser les méthodes du repository. Je te laisse copier le code suivant dans __AnnonceController__ :
```php
    use App\Repository\AnnonceRepository; // ne pas oublier
    // ... reste du code
    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        // rechercher une annonce par ID
        $annonce = $annonceRepository->find(1);
        dump($annonce);
        // recherche toutes les annonces
        $annonce = $annonceRepository->findAll();
        dump($annonce);
        // recherche une annonce par champ
        $annonce = $annonceRepository->findOneBy(['isSold' => false]);
        dump($annonce);

        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index'
        ]);
    }
```

Si tu actualises la page, tu peux voir tes objets dans la debug bar, en passant ta souris sur la petite cible en bas. N'hésite pas à aller y jeter un œil !

Il faudrait afficher toutes les annonces qui ne sont pas vendues, on voit que le _findOneBy_ ne retourne qu'un seul résultat... Tu peux donc utiliser le repository.  
Dans __/src/Repository/AnnonceRepository.php__, ajoute ceci:
```php
/**
 * @return Annonce[]
 */
public function findAllNotSold(): array
{
    return $this->createQueryBuilder('a')
        ->andWhere('a.sold = false')
        ->getQuery() // permet de créer un objet utilisable pour récupérer le résultat
        ->getResult() // permet de récupérer le résultat
    ;
}
```
Et dans la fonction __index__ de __AnnonceController__ :
```php
$annonces = $annonceRepository->findAllNotSold();
dump($annonces);
```

## Affiche les annonces
Ajoutes différents biens comme tu l'as fait avant, en rechargeant la page ```/annonce/new``` plusieurs fois (on verra une meilleure façon de faire plus tard).  
Recharger la page aura pour effet de relancer la fonction __new__ et donc d'insérer à chaque fois une nouvelle annonce en base de données.
![F5](https://media.giphy.com/media/vyVxeMNGUBT7q/giphy.gif)

### Exercice
Tente d'afficher toutes les annonces depuis la méthode ```index``` de ```AnnonceController```:
- récupère toutes les annonces dans ```index``` grâce à ```$annonceRepository->findAllNotSold()```;
- envoie toutes les annonces au template;
- fais une [boucle](https://twig.symfony.com/doc/3.x/tags/for.html) pour afficher chaque annonce dans un élément de liste HTML ;
- pour afficher une propriété d'un objet en Twig, il faut écrire ```{{ object.property }}```.
---
---
---
![Later](https://static.boredpanda.com/blog/wp-content/uploads/2019/11/20-5dce8cedac70f__700.jpg)  

---
---
---

#### Correction
Modifie __AnnonceController__ :
```php
public function index(AnnonceRepository $annonceRepository)
{
    $annonces = $annonceRepository->findAllNotSold();

    return $this->render('annonce/index.html.twig', [
        'current_menu' => 'app_annonce_index',
        'annonces' => $annonces,
    ]);
}
```
Et ajoute ceci au template __templates/annonce/index.html.twig__ :
```html
{# ... #}
{% block content %}
<div class="container">
    <h1>Liste des annonces</h1>
    <ul>
        {% for annonce in annonces %}
            <li>{{ annonce.title }}</li>
        {% endfor %}
    </ul>
</div>
{% endblock %}
{# ... #}
```
Si tu actualises la page, tu auras la liste avec des annonces non vendues.

Ajoutes le fichier __templates/annonces/_card.html.twig__ avec ce contenu : 
```html
<div class="card mb-4 shadow-sm" height="225" width="100%">
    <img src="https://via.placeholder.com/400?text=Plastic Duck {{ key }}" class="card-img-top" alt="Plastic Duck {{ key }}">
    <div class="card-body">
        <h5 class="card-title">{{ annonce.title }}</h5>
        <p class="card-text">{{ annonce.description }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <div class="btn-group">
                <a class="btn btn-sm btn-outline-secondary" type="button">View</a>
                <a class="btn btn-sm btn-outline-secondary" type="button">Edit</a>
            </div>
            <small class="text-muted">{{ annonce.price }}€</small>
        </div>
    </div>
</div>

```
Ce template nous servira à plusieurs endroits. Donc plutôt que de faire du copier/coller, autant utiliser un template inclue.

Voici à quoi doit ressembler le template au template __templates/annonce/index.html.twig__:
```html
{% extends '_layout/sidebar.html.twig' %}
{% block title %}Les annonces - DuckZon !{% endblock %}
{% block content %}
<div class="container">
    <h1>Liste des annonces</h1>
    <div class="row">
        {% for key, annonce in annonces %}
        <div class="col-md-4">
            {{ include('annonce/_card.html.twig', {annonces: annonces, key: key}) }}
        </div>
        {% endfor %}
    </div>
</div>
{% endblock %}
```

### Exercices
Crée une méthode __findLatestNotSold__ dans le repository qui retournera les 3 dernières annonces.

Affiches ces annonces sur la home grâce à twig.

Pour t'aider, tu peux commencer par faire la requête en SQL classique et aller voir sur [la documentation de Doctrine](https://www.doctrine-project.org/index.html)

---
---
---
![Later](https://i.ytimg.com/vi/9jY4d6mGAUA/maxresdefault.jpg)  

---
---
---

#### Correction
Dans __src/Repository/AnnonceRepository.php__ :
```php
// ...
use Doctrine\ORM\QueryBuilder;
// ...

class AnnonceRepository extends ServiceEntityRepository
{
    // ...
    /**
     * @return Annonce[]
     */
    public function findAllNotSold(): array
    {
        return $this->findNotSoldQuery()
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Annonce[]
     */
    public function findLatestNotSold(): array
    {
        return $this->findNotSoldQuery()
            ->setMaxResults(3)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return QueryBuilder
     */
    private function findNotSoldQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.sold = false')
        ;
    }
    
    //...
}
```

Dans __src/Controller/HomeController.php__ :
```php
<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findLatestNotSold();
        return $this->render('home/index.html.twig', [
            'current_menu' => 'app_home_index',
            'annonces' => $annonces
        ]);
    }
}

```
Et dans __templates/home/index.html.twig__ :
```html
{% extends '_layout/full-width.html.twig' %}

{% block title %}Bienvenue sur DuckZon !{% endblock %}

{% block content %}
<div class="container">
    <div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
        <h1 class="display-4">SmallAds</h1>
        <p class="lead">Vends toutes tes affaires !</p>
    </div>
</div>

<div class="container">
    <h2>Les dernières annonces</h2>
    <div class="row">
        {% for key, annonce in annonces %}
        <div class="col-md-4">
            {{ include('annonce/_card.html.twig', {annonces: annonces, key: key}) }}
        </div>
        {% endfor %}
    </div>
</div>
{% endblock %}
```

## Détail d'une annonce

En suivant le chemin  ```/annonce/{id de l'annonce}```, il faudrait afficher le détail d'une annonce. En clair, si je tape http://127.0.0.1:8000/annonce/2, je devrais tomber sur le détail de l'annonce d'id 2 en base de données.

### Exercice
- Dans __AnnonceController__, crée une nouvelle méthode ```show``` ;
- cette méthode répond à la route ```/annonce/{id}```. Cet id __doit__ forcément être un _int_, sinon la route ne devra pas correspondre. Pour en apprendre plus sur les routes, c'est [par ici](https://symfony.com/doc/current/routing.html) ;
- dans cette méthode, tu peux chercher l'annonce avec son id grâce à son repository. Vas voir dans le fichier __AnnonceRepository__ pour voir les méthodes auxquelles tu as accès si tu as un doute ;
- envoie l'annonce trouvée à la vue Twig que tu auras préalablement créée ```templates/annonce/show.html.twig``` ;
- si l'annonce n'est pas trouvée, envoie une erreur 404 (https://symfony.com/doc/current/controller.html#managing-errors-and-404-pages) ;
- affiche les détails de l'annonce dans ce template.

---
---
---
![Later](https://i.ytimg.com/vi/xYqz04A2WEw/maxresdefault.jpg)  

---
---
---

#### Correction
10 secondes ! Tu commences à être un pro !

Ajoute cette méthode au controller __src/Controller/AnnonceController.php__ :
```php
    #[Route('/annonce/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, AnnonceRepository $annonceRepository): Response
    {

        $annonce = $annonceRepository->find($id);

        if (!$annonce) {
            return $this->createNotFoundException();
        }
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
```
Ou encore mieux, tu peux utiliser le [__ParamConverter__](https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html) !  
Dès qu'il y a un paramètre de route qui correspond à une propriété de ton entité (ici __id__), Symfony se chargera lui-même d'instancier l'entité s'il la trouve en base de données, ou de lever une 404 si cette entité n'existe pas :
```php
    #[Route('/annonce/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
```
Tu peux vérifier que le code de réponse de chaque page dans la debug bar en bas à gauche.

Enfin, crée le template __templates/annonce/show.html.twig__ :
```html
{% extends '_layout/full-width.html.twig' %}

{% block title %}#{{ annonce.id }}-{{ annonce.title }}!{% endblock %}
{% block content %}
<div class="container">
    <div class="row mt-4">
        <div class="col-md-8">
            <img class="img-fluid" src="https://via.placeholder.com/800?text=Plastic Duck {{ annonce.id }}">
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h4 class="my-0 font-weight-normal">{{ annonce.title }}</h4>
                </div>
                <div class="card-body">
                    <p class="h1 card-title pricing-card-title">
                        {{ annonce.price }} €
                    </p>

                    <p>
                        {% if annonce.status == constant('App\\Entity\\Annonce::STATUS_VERY_BAD') %}
                        <span class="badge bg-danger">État très mauvais</span>
                        {% elseif annonce.status == constant('App\\Entity\\Annonce::STATUS_BAD') %}
                        <span class="badge bg-warning">Mauvais état</span>
                        {% elseif annonce.status == constant('App\\Entity\\Annonce::STATUS_GOOD') %}
                        <span class="badge bg-primary">Bon état</span>
                        {% elseif annonce.status == constant('App\\Entity\\Annonce::STATUS_VERY_GOOD') %}
                        <span class="badge bg-info">Très bon état</span>
                        {% elseif annonce.status == constant('App\\Entity\\Annonce::STATUS_PERFECT') %}
                        <span class="badge bg-success">Parfait état</span>
                        {% endif %}
                    </p>
                    <button class="btn btn-lg btn-block btn-outline-primary" type="button">Contacter le vendeur</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row mt-4">
        <div class="col-md-12">
            <h2>Déscription</h2>
            <p>{{ annonce.description }}</p>
        </div>
    </div>
</div>
{% endblock %}
```

## Slug
Sur https://packagist.org/, tu peux trouver un tas de librairie qui peuvent être installées dans ton projet via Composer. N'hésites pas à chercher si une librairie existe pour répondre à ton besoin.

Ici, nous souhaitons générer des liens seo-friendly pour nos annonces. Imaginons que le titre d'une annonce soit le suivant : _"Vends Super Canard trouvé en boulangerie-pâtisserie"_. Il faudrait pouvoir accéder à cette annonce via une url du genre _/annonce/vends-super-canard-trouve-en-boulangerie-patisserie_.

Nous allons utiliser [slugify](https://packagist.org/packages/cocur/slugify) pour générer des liens corrects vers les annonces.  

Il convient d'ajouter cette librairie au projet en tapant :
``` console
composer require cocur/slugify
```

### Exercice
- Ajoute une propriété _slug_/_255_/notNull à l'entité __Annonce__ ;
- met à jour la base de données ;
- dans la création d'une annonce (_/annonce/new_), tu peux ajouter ```->setSlug('Vends Super Canard trouvé en boulangerie-pâtisserie')``` avant l'enregistrement en base de données pour ajouter un slug à l'annonce ;
- dans l'entité __Annonce__, modifies la fonction ```setSlug``` afin de transformer le paramètre ```$slug``` en slug valide (grâce à la lib Slugify) ;
- dans __AnnonceController.php__, modifie la fonction ```show``` pour qu'elle réponde à la route ```/annonce/{slug}-{id}``` ;
- cette fonction va chercher en base de données l'annonce selon le slug et l'id passés dans la route, et affiche le détail de l'annonce.

---
---
---
![Later](https://i.ytimg.com/vi/DH0BHW5Il-k/maxresdefault.jpg)  

---
---
---
#### Correction
```php
# src/Entity/Annonce.php
/**
 * En haut du fichier
 */
use Cocur\Slugify\Slugify;
/**
 * Le reste du code
 */
public function getSlug(): ?string
{
    if (!$this->slug) {
        $this->setSlug($this->title);
    }
    return $this->slug;
}

public function setSlug(string $slug): self
{
    $slugify = new Slugify();
    $this->slug = $slugify->slugify($slug);

    return $this;
}
```

Et modifie la fonction __show__ du controller __src/Controller/AnnonceController.php__ :
```php
// ...
#[Route('/annonce/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'])]
public function show(string $slug, int $id, AnnonceRepository $annonceRepository): Response
{
    $annonce = $annonceRepository->findOneBy([
        'slug' => $slug,
        'id' => $id
    ]);

    if (!$annonce) {
        return $this->createNotFoundException();
    }

    return $this->render('annonce/show.html.twig', [
        'annonce' => $annonce,
    ]);
}
```

Tu peux aussi utiliser le __Param Converter__ :
```php
// ...
#[Route('/annonce/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'])]
public function show(Annonce $annonce): Response
{
    return $this->render('annonce/show.html.twig', [
        'annonce' => $annonce,
    ]);
}
```

Dans tes templates, ajoute le lien vers les annonces :
```html
<a href="{{ path('app_annonce_show', {id: annonce.id, slug: annonce.slug}) }}" class="btn btn-sm btn-outline-secondary" type="button">View</a>
```

## Evénements Doctrine
Il est possible d'améliorer un peu l'enregistrement du slug et de la date de création de l'entité Annonce. 
En effet, ce que nous avons fait est correct, mais Doctrine nous permet d'utiliser des événements qui se déclenchent lors du cycle de vie d'une entité : 
- avant un persit,
- avant un enregistrement en base de données, 
- après le chargement depuis la base de données, 
- etc...

Dans certains cas, tu peux avoir besoin d'effectuer des actions juste avant ou juste après la création, la mise à jour ou la suppression d'une entité. 
Par exemple, si tu stockes la date d'édition d'une annonce, à chaque modification de l'entité __Annonce__ il faut mettre à jour cet attribut juste avant la mise à jour dans la base de données.

Ces actions, tu dois les faire à chaque fois. 
Cet aspect systématique a deux impacts : 
- d'une part, cela veut dire qu'il faut être sûrs de vraiment les effectuer à chaque fois, pour que ta base de données soit cohérente, 
- d'autre part, cela veut dire qu'on est bien trop fainéants pour se répéter !

C'est ici qu'interviennent les évènements Doctrine.  
Plus précisément, les callbacks du cycle de vie (lifecycle en anglais) d'une entité.  
Un callback est une méthode de ton entité, et on va dire à Doctrine de l'exécuter à certains moments.

On parle d'évènements de « cycle de vie », car ce sont différents évènements que Doctrine déclenche à chaque moment de la vie d'une entité : 
- son chargement depuis la base de données, 
- sa modification, 
- sa suppression, 
- etc. 

Pour en savoir plus, tu peux lire la [documentation](https://symfony.com/doc/current/doctrine/events.html) et regarder cette [vidéo](https://www.youtube.com/watch?v=-HoTTylU3to). 
Cette fonctionnalité te sera surement utile plus tard, ne passe pas à côté.

Je te propose donc de modifier légèrement l'entité __Annonce__ de la façon suivante :
```php
# src/Entity/Annonce.php
// ...
/**
 * ...
 * @ORM\HasLifecycleCallbacks()
 */
class Annonce
{
    // reste du code ...
    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->slug = (new Slugify())->slugify($this->title);
    }

    // plus besoin du constructeur
    /*public function __construct()
    {
        $this->createdAt = new \DateTime();
    }*/
```

Dans la fonction __new__ de __AnnonceController__, nous pouvons supprimer les lignes 
```php
->setCreatedAt(new \DateTimeImmutable())
->setSlug($annonce->getTitle())
```
```php
    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine): Response
    {
        $annonce = new Annonce();
        $annonce
            ->setTitle('Ma collection de canards')
            ->setDescription('Vends car plus d\'utilité')
            ->setPrice(10)
            ->setStatus(Annonce::STATUS_BAD)
            ->setIsSold(false)
            //->setCreatedAt(new \DateTimeImmutable()) // à supprimer
            //->setSlug($annonce->getTitle()) // à supprimer
        ;

```

Essaye maintenant de recharger la page _annonce/new_ et vérifie que l'annonce s'est bien enregistrée avec un slug.

> Bravo si tu es arrivé jusque-là sans encombre, on se retrouve après pour la suite   
![Bravo](https://media.giphy.com/media/Swx36wwSsU49HAnIhC/giphy.gif)
