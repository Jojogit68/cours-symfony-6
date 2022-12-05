# Paginer les annonces
> Les documentations seront tes meilleures amies si tu souhaite progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendra sûrement un jour si tu ne lis pas les documentations).  

Il est nécessaire de prévoir une pagination, car si notre site fonctionne, il y aura certainement beaucoup (beaucoup) d'annonces ! Hé oui ! Plein de monde souhaite collectionner des canards !

Nous allons donc créer des données de test, pour tester nos développements, comme si un plusieurs utilisateurs avaient ajouté plusieurs annonces.  
Nous allons ajouter une centaine, que dis-je, un millier d'annonces ! Grâce aux [Fixtures](https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html).

## Fixtures
Nous n'avons presque pas d'annonces en base de données. Symfony nous met à disposition un package qui nous permettra de remplir le base de données pour nos développements.  
Il faut en premier lieu installer ce package:
``` console
composer require orm-fixtures --dev
```

En complément, nous allons utiliser la librairie [Fake PHP](https://fakerphp.github.io/) qui permet de générer du texte, des nombres, ou encore d'autres données de façon aléatoire.

Pour l'utiliser, il faut l'installer, ce que je t'invite à faire :
```shell
composer require fakerphp/faker
```
Voici un exemple d'utilisation :
```php
use Faker\Factory;
// et l'initialiser
$faker = Factory::create('fr_FR');
// pour générer du texte :
$faker->words(3, true);
// pour générer une phrase :
$faker->sentences(3, true);
// pour générer des nombres :
$faker->numberBetween(0, 4);
// ...$faker->numberBetween(0, 4)
```
 

Crées ta première fixture :
```shell
php bin/console make:fixture

 The class name of the fixtures to create (e.g. AppFixtures):
 > AnnonceFixtures

 created: src/DataFixtures/AnnonceFixtures.php

           
  Success! 
           

 Next: Open your new fixtures class and start customizing it.
 Load your fixtures by running: php bin/console doctrine:fixtures:load
 Docs: https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
```

Symfony a généré un fichier __src/DataFixtures/AnnonceFixtures.php__.

Tu peux l'ouvrir et le modifier ainsi et ce qui doit t'interpeller sont les lignes suivantes :
```php
# src/DataFixtures/AnnonceFixtures.php
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AnnonceFixtures extends Fixture
{
    // On demande à Symfony l'ObjectManager, qui permet d'insérer ou de mettre à jour la base de données
    public function load(ObjectManager $manager) 
    {
        $product = new Product(); // création d'une nouvelle instance de Product (cette entité n'existe pas, c'est pour l'exemple)
        $manager->persist($product); // cette ligne permet de dire à Doctrine que l'objet $product doit être inséré en base de données
        // on pourrait imaginer hydrater $product avec des setter
        $product->setName('Canard de compétition');

        $manager->flush(); // cette ligne permet d'envoyer les objets persistés en base de données
    }
}
```

### Exercice
En suivant la logique plus haut, essaie dans un premier temps de créer une annonce en l'hydratant avec des données que tu auras choisies (```$annonce->setTitle('Mon annonce de canard')```).

Et lance tes fixture depuis un terminal :
```shell
php bin/console doctrine:fixture:load
```
Tu devrais avoir une nouvelle annonce en base de données.

Maintenant, essaye de créer 1000 annonces (bon en vrai si ta machine n'est pas très puissante, n'hésites pas à réduire le nombre à une centaine...) en les hydratant avec des données aléatoires. Regarde bien la documentation de [Faker PHP](https://fakerphp.github.io/).

---
---
---
![Later](https://i.ytimg.com/vi/TXUt7toOsMM/maxresdefault.jpg)  

---
---
---

#### Correction
```php
<?php
# src/DataFixtures/AnnonceFixture.php
namespace App\DataFixtures;

use App\Entity\Annonce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AnnonceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        for ($i=0; $i < 1000; $i++) {
            $annonce = new Annonce();
            $annonce
                ->setTitle($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setPrice($faker->numberBetween(10, 100))
                ->setStatus($faker->numberBetween(0, 4))
                ->setIsSold(false)
            ;
            $manager->persist($annonce);
        }

        $manager->flush();
    }
}

```
Et lance ta fixture :
```shell
php bin/console doctrine:fixture:load
```

Tu as maintenant 1000 annonces en base de données !

## Bundle
Un [bundle](https://symfony.com/doc/current/bundles.html) est en quelque sorte un plugin pour Symfony, qui va te permettre 
d'ajouter des fonctionnalités dans tes applications, et ainsi pouvoir t'éviter de développer toute une fonctionnalité déjà codée.

Par exemple pour ajouter un système de pagination, on peut soit :
- développer tout le système de pagination pour notre application (ce qui est tout à fait possible) ;
- chercher si des développeurs n'ont pas déjà créé une librairie (un bundle dans notre cas) qui répond à notre besoin.

Je te propose de chercher le mot __pagination__ sur https://packagist.org/. Tu remarqueras qu'il y a déjà pas mal de librairie résultant de cette recherche. 
Dont __knp-paginator-bundle__ que je te propose d'utiliser.

### knp-paginator-bundle
Nous allons voir comment utiliser un bundle, et en l'occurrence, un bundle permettant de créer une pagination, il s'agit de :
https://packagist.org/packages/knplabs/knp-paginator-bundle

> Avant de le lancer dans la suite, je te propose de lire un petit peu la documentation de cette librairie. Le but sera de créer une pagination, 
> aux endroits où toutes les annonces sont listées. 

Dans un premier temps, il faut installer ce bundle grâce à composer :
```shell
composer require knplabs/knp-paginator-bundle
```
Il faut ensuite définir la configuration, et pour cela tu peux créer un fichier __config/packages/knp_paginator.yaml__ et ajouter le code suivant (tout est écrit dans la doc du bundle) :
```yaml
knp_paginator:
    page_range: 5                       # number of links showed in the pagination menu (e.g: you have 10 pages, a page_range of 3, on the 5th page you'll see links to page 4, 5, 6)
    default_options:                                 
        page_name: page                 # page query parameter name
        sort_field_name: sort           # sort field query parameter name
        sort_direction_name: direction  # sort direction query parameter name
        distinct: true                  # ensure distinct results, useful when ORM queries are using GROUP BY statements
        filter_field_name: filterField  # filter field query parameter name
        filter_value_name: filterValue  # filter value query parameter name
    template:                                        
        pagination: '@KnpPaginator/Pagination/sliding.html.twig'     # sliding pagination controls template         
        sortable: '@KnpPaginator/Pagination/sortable_link.html.twig' # sort link template                                
        filtration: '@KnpPaginator/Pagination/filtration.html.twig'  # filters template
```

Dans __src/Repository/AnnonceRepository.php__ ajoute cette méthode :
```php
    //...
    use Doctrine\ORM\Query; // ne pas oublier ce use
    //...
    public function findAllNotSoldQuery(): Query // knp_paginator aura besoin d'un objet de type Query pour fonctionner, c'est pour cela que nous avons besoin de cette fonction
    {
        return $this->findNotSoldQuery()
            ->getQuery()
        ;
    }
```

Et dans la méthode __index__ du controller __src/Controller/AnnonceController.php__, tu peux injecter directement 
PaginatorInterface grâce à l'autowiring et l'utiliser comme ceci :
```php
    //...
    use Knp\Component\Pager\PaginatorInterface; // ne pas oublier ce use
    //...
    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $annonces = $paginator->paginate(
            $annonceRepository->findAllNotSoldQuery(),
            $request->query->getInt('page', 1), // on récupère le paramètre page en GET. Si le paramètre page n'existe pas dans l'url, la valeur par défaut sera 1
            12 // on veut 12 annonces par page
        );

        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index',
            'annonces' => $annonces
        ]);
    }
```
Et pour afficher la pagination, dans le template __templates/annonce/index.html.twig__:
```php
{# reste du code #}
<div class="pagination">
    {{ knp_pagination_render(annonces) }}
</div>
```

Enfin, tu peux changer le style de l'élément en changeant la config __config/packages/knp_paginator.yaml__ :
```yaml
# ....
# reste de la config
template:                                        
    pagination: '@KnpPaginator/Pagination/bootstrap_v5_pagination.html.twig'     # sliding pagination controls template         
```

## Pagination sans Bundle:

### Les requêtes
Pour créer un système de pagination, il faut trois choses : 
- Le nombre total d'annonces ```$totalAnnonce``` que l'on peut avoir avec une requête 
```php 
  $queryBuilder->select('COUNT(a.id)')
```
- Le nombre total de page par rapport au nombre d'annonce par page que l'on peut obtenir en faisant 
```php
    $perPage = 21;
    $totalPages = ceil($totalAnnonce/$perPage)
```
- Les annonces en utilisant l'instruction [LIMIT](https://sql.sh/cours/limit) de MYSQL, que l'on peut obtenir avec une requête
```php
    ->setFirstResult(($page-1) * $perPage)
    ->setMaxResults($perPage)
``` 
Voici ce que cela pourrait donner dans un repository :
```php
    public function findAllNotSoldPaginate($page = 0, $perPage = 10)
    {
        return $this->findNotSoldQuery()
            ->setFirstResult(($page-1) * $perPage)
            ->setMaxResults($perPage)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed The scalar result, or NULL if the query returned no result.
     */
    public function findTotalNotSold()
    {
        return $this->findNotSoldQuery()
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function findNotSoldQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.sold = false')
        ;
    }
    
```
Dans une méthode de contrôleur :
```php
   
    public function index(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $totalAnnonce = $annonceRepository->findTotalNotSold();
        $perPage = 21;
        $totalPages = ceil($totalAnnonce/$perPage);
        
        $page = $request->get('page');
        
        if ($page === null || $page > $totalPages || $page < 1) {
            $page = 1;
        }
        $annonces = $annonceRepository->findAllNotSoldPaginate($page, $perPage);

        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index',
            'annonces' => $annonces,
            'total_pages' => $totalPages,
            'page' => $page
        ]);
    }
```
Le template de pagination que tu pourrais créer comme ceci : __templates/_inc/_pagination.html.twig__ :
```php
<nav aria-label="Page navigation example">
    <ul class="pagination">
        <li class="page-item"><a class="page-link" href="{{ path('app_annonce_index') }}?page={{ page - 1 }}">Previous</a></li>
        {% for i in range(1, total_pages) %}
            <li class="page-item{% if i == page %} active{% endif %}">
                <a 
                    class="page-link" 
                    href="{{ path('app_annonce_index') }}?page={{ i }}"
                >
                    {{ i }}
                </a>
            </li>
        {% endfor %}
        <li class="page-item"><a class="page-link" href="{{ path('app_annonce_index') }}?page={{ page + 1 }}">Next</a></li> 
    </ul>
</nav>
```
Que tu vas inclure dans __templates/annonce/index.html.twig__ :
```php
{{ include("includes/_pagination.html.twig", {
    total_pages: total_pages,
    current_page: page
}) }}
```

Voilà, tu as désormais un système de pagination. Bien joué !
![pagination](https://media.giphy.com/media/BWEY1LI6WdaN2/giphy.gif)
