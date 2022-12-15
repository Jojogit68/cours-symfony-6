# Rechercher des annonces
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

Le but ici est de créer un formulaire permettant de rechercher des annonces selon différents critères :
- titre ;
- prix maximum ;
- status ;
- etc...

Nous avons plusieurs solutions pour créer le formulaire :
- créer un formulaire en HTML ;
- profiter du système de formulaire de Symfony.

Je te propose de profiter du système de formulaire de Symfony.
Pour cela, il est possible de créer une entité qui ne sera pas reliée à la base de données,
mais qui nous permettra tout de même de créer un formulaire en se basant sur les propriétés de cette entité et
de stocker les données du formulaire dans cette entité.

## Création du formulaire
Tu peux ajouter le fichier __src/Entity/AnnonceSearch.php__ :
```php
<?php

namespace App\Entity;

class AnnonceSearch
{
    private ?string $title = null;

    private ?int $status = null;

    private ?int $maxPrice = null;

    private ?\DateTimeInterface $createdAt = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?int $maxPrice = null): self
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status = null): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
```

Depuis la version de 5 de Symfony, il n'est plus possible de générer un formulaire d'une entité non reliée à la base de données avec ```php bin/console make:form```...  
Ce n'est pas grave, il est toujours possible de le créer à la main :
```php
<?php
#src/Form/AnnonceSearchType.php

namespace App\Form;

use App\Entity\AnnonceSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Annonce;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AnnonceSearchType extends AbstractType
{
    public $routeGenerator;

    public function __construct(UrlGeneratorInterface $routeGenerator)
    {
        $this->routeGenerator = $routeGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre'
                ]
                
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prix maximum'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Très mauvais' => Annonce::STATUS_VERY_BAD,
                    'Mauvais'      => Annonce::STATUS_BAD,
                    'Bon' => Annonce::STATUS_GOOD,
                    'Très bon' => Annonce::STATUS_VERY_GOOD,
                    'Parfait' => Annonce::STATUS_PERFECT
                ],
                'label' => false,
                'required' => false,
                'placeholder' => 'État',
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Créé après le',
                'required' => false,
            ]) 
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnnonceSearch::class,
            'method' => 'get', // lors de la soumission du formulaire, les paramètres transiteront dans l'url. Utile pour partager la recherche par exemple
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        // permet d'enlever les préfixe dans l'url. Tu peux commenter cette fonction, soumettre le formulaire et regarder l'url pour voir la différence.
        return '';
    }
}
```
## Affichage du formulaire
Nous pouvons maintenant afficher ce formulaire de façon classique, comme nous l'avons fait jusqu'à maintenant :
```php
# dans un contrôleur
# ceci est un exemple !
#[Route('/une-route')]
public function recherche(Request $request): Response
{
    $search = new AnnonceSearch();
    $form = $this->createForm(AnnonceSearchType::class, $search);
    $form->handleRequest($request);
    //...

    return $this->render('template.html.twig', [
        'search' => $form->createView(),
        //...
    ]);
```
Mais il ne sera affiché que sur la route __/une-route__...
L'idéal serait d'afficher ce formulaire sur toutes les pages, dans le header du site par exemple.

Alors comment afficher ce formulaire sur toutes les pages ? Cela serait super si depuis un template Twig, on pouvait appeler une méthode d'un controller et afficher son résultat !

Eh bien, c'est possible avec le système d'[intégration du résultat de l'exécution d'un contrôleur](https://symfony.com/doc/current/templates.html#embedding-controllers).
Je te laisse lire la [documentation](https://symfony.com/doc/current/templates.html#embedding-controllers) pour en comprendre les tenants et les aboutissants.
> Sache simplement que cette façon de procéder nécessite de faire des requêtes aux contrôleurs appelés et de rendre certains templates en conséquence.
> Cela peut avoir un impact significatif sur les performances de l'application si tu intégres de nombreux contrôleurs.
> Pour remédier à cela, il est possible de mettre en cache les _"fragments"_ de templates avec [Edge Side Includes](https://symfony.com/doc/current/http_cache/esi.html).

### Render esi
La fonction Twig ```render_esi``` permet d'afficher le retour d'une méthode de contrôleur dans un template Twig avec une mise en cache du résultat. Cela tombe bien le formulaire ne va pas changer toutes le 5 min !

Pour activer cette fonctionnalité, il faut dé-commenter une petite ligne dans __config/packages/framework.yaml__ :
```yaml
framework:
    #...
    esi: true
    #fragments: true

```
On pourrait maintenant imaginer une méthode qui s'occupe d'afficher le formulaire, qui procède à la recherche lors de la soumission de ce dernier et qui affiche la liste des résultats.

#### Exercice
Je te laisse essayer d'afficher le formulaire de recherche dans le menu, grâce à ```render_esi```.
Pour cela il faut :
- créer un nouveau controller __AnnonceSearchController__ avec la ligne de commande ;
- dans la méthode __index__ de ce controller, affiche le formulaire __AnnonceSearchType__ (tu sais, grâce à ```$this->createForm(...)```) et ```return $this->render('annonce_search/index.html.twig', [
  'form' => $form->createView(),
  ]);``` ;
- dans le template du menu, appelle la fonction ```render_esi``` avec la méthode du contrôleur ```render_esi(controller(...```

---
---
---
![later](https://i.ytimg.com/vi/mVKN3Lrir2o/maxresdefault.jpg)

---
---
---

##### Correction

Tu peux donc créer un nouveau controller avec la commande ```php bin/console make:controller AnnonceSearchController```.

Une fois le controller généré, rends-toi dans __src/Controller/AnnonceSearchController.php__ :
```php
<?php

namespace App\Controller;

use App\Entity\AnnonceSearch;
use App\Form\AnnonceSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceSearchController extends AbstractController
{
    #[Route('/annonce/search', name: 'app_annonce_search', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $annonceSearch = new AnnonceSearch();
        $form = $this->createForm(AnnonceSearchType::class, $annonceSearch);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on fera le traitement plus tard
        }

        return $this->render('annonce_search/_search-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```

Tu peux donc aussi créer un template __templates/annonce_search/_search-form.html.twig__ qui contiendra simplement le code suivant :
```php
{{ form(form) }}
```

> En allant sur __/annonce/search__, tu devrais voir le formulaire s'afficher.

Il reste maintenant à afficher le retour de la méthode __index__ (le formulaire) dans un template twig.
Par exemple dans __templates/\_inc/nav.html.twig__ :
```html
<nav>
  <!-- reste du code -->
  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#search" aria-expanded="false" aria-controls="search">
    Recherche avancée
  </button>
  <!-- reste du code -->
</nav>
<div class="collapse" id="search">
  <div class="card card-body">
    {{ render_esi(controller('App\\Controller\\AnnonceSearchController::index')) }}
  </div>
</div>
```

Si tu vas voir sur n'importe quelle page, tu vois que le formulaire de recherche s'affiche toujours dans le menu grâce à ```render_esi```.

## Traitement du formulaire
Voici comment je te propose de traiter le formulaire lors de sa soumission :
- nous allons créer une nouvelle méthode __findByAnnonceSearchQuery__ dans __src/Repository/AnnonceRepository.php__ qui ira chercher en base de données (et oui, on ne va pas sortir les annonces de notre... CHAPEAU !) ;
- nous allons appeler cette méthode quand le formulaire est envoyé, avec en paramètres, la variable __$annonceSearch__ qui contient __toutes__ les informations du formulaire sous forme d'objet __AnnonceSearch__.

Voici donc à quoi pourrait ressembler la méthode __findByAnnonceSearchQuery__ dans __src/Repository/AnnonceRepository.php__ :
```php
#src/Repository/AnnonceRepository.php
//...
use App\Entity\AnnonceSearch;
//...
public function findByAnnonceSearchQuery(AnnonceSearch $annonceSearch): Query
{
    $query = $this->createQueryBuilder('a');
    if ($annonceSearch->getCreatedAt() !== null) {
        $query
            ->andWhere('a.createdAt > :createdAt')
            ->setParameter(':createdAt', $annonceSearch->getCreatedAt())
        ;
    }
    //...
    // autres conditions ce qui est contenu dans $annonceSearch
    //...
    return $query->getQuery(); // on retourne un objet Query car nous voulons utiliser KnpPaginator pour afficher la liste des annonces
}
```
Et voici comment l'appeler dans le contrôleur __src/Controller/AnnonceSearchController.php__ :
```php
<?php
//...

use App\Repository\AnnonceRepository;
use Knp\Component\Pager\PaginatorInterface;

//...

#[Route('/annonce/search', name: 'app_annonce_search', methods: ['GET'])]
public function index(Request $request, AnnonceRepository $annonceRepository, PaginatorInterface $paginator)
{
    // reste du code
    if ($form->isSubmitted()) {
       $annonces = $paginator->paginate(
            $annonceRepository->findByAnnonceSearchQuery($annonceSearch),
            $request->query->getInt('page', 1),
            12
        );
        dump($annonces);
    }
```

### Exercice
Je te laisse essayer de faire la requête dans __src/Repository/AnnonceRepository.php__, selon ce qui est envoyé dans le formulaire.

![Later](https://i.ytimg.com/vi/Uqn7V-Fjjq0/maxresdefault.jpg)

#### Correction
```php
#src/Repository/AnnonceRepository.php
public function findByAnnonceSearchQuery(AnnonceSearch $annonceSearch): Query
{
    $query = $this->createQueryBuilder('a');
    if ($annonceSearch->getCreatedAt() !== null) {
        $query
            ->andWhere('a.createdAt > :createdAt')
            ->setParameter(':createdAt', $annonceSearch->getCreatedAt())
        ;
    }

    if ($annonceSearch->getTitle() !== null) {
        $query
            ->andWhere('a.title LIKE :title')
            ->setParameter('title', '%'.$annonceSearch->getTitle().'%')
        ;
    }

    if ($annonceSearch->getStatus() !== null) {
        $query
            ->andWhere('a.status = :status')
            ->setParameter('status', $annonceSearch->getStatus())
        ;
    }

    if ($annonceSearch->getMaxPrice() !== null) {
        $query
            ->andWhere('a.price < :maxPrice')
            ->setParameter('maxPrice', $annonceSearch->getMaxPrice())
        ;
    }

    return $query->getQuery();
}
```

## Recherche par Tag
Dans le formulaire de recherche, pourquoi pas ajouter la possibilité de rechercher par tag.

![TAG](https://media.giphy.com/media/bqbOgHSuhy257hNBYA/giphy.gif)
### Exercice
- Ajoute un champ (un select) permettant de choisir un ou plusieurs tags (tu peux t'aider de la [documentation](https://symfony.com/doc/current/reference/forms/types/entity.html)).

---
---
---
![Later](https://i.ytimg.com/vi/TXUt7toOsMM/maxresdefault.jpg)

---
---
---

#### Correction
Dans __src/Form/AnnonceSearchType.php__ :
```php
// n'oublions pas ces use
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Tag;
// reste du code...
->add('tags', EntityType::class, [
    'required' => false,
    'label' => false,
    'class' => Tag::class,
    'choice_label' => 'name',
    'multiple' => true
])  
```
Et il ne faut pas oublier d'ajouter la propriété tags à __src/Entity/AnnonceSearch.php__
```php
<?php
//...
use Doctrine\Common\Collections\ArrayCollection;
//...

    // la propriété
    private ?ArrayCollection $tags = null;

    // ...

    // les getters et setters
    public function getTags(): ?ArrayCollection
    {
        return $this->tags;
    }
    
    public function setTags(?ArrayCollection $tags): self
    {
        $this->tags = $tags;
        
        return $this;
    }
```

## La requête de recherche
Pour la requête, tu vas avoir besoin de [DQL](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/dql-doctrine-query-language.html)
Dans les exemples de la documentation, Doctrine propose des exemples dont celui-ci :
```php
<?php
$query = $em->createQuery('SELECT u.id FROM CmsUser u WHERE :groupId MEMBER OF u.groups');
$query->setParameter('groupId', $group);
$ids = $query->getResult();
```
Celui-ci semble convenir à notre cas. Dans __src/Repository/AnnonceRepository.php__, dans la méthode __findByAnnonceSearchQuery__ tu peux ajouter ce code dans le bloc qui s'occupe de la recherche :
```php
if ($annonceSearch->getTags()->count() > 0) {
    $cpt = 0;
    foreach ($annonceSearch->getTags() as $key => $tag) {
        $query = $query
            ->andWhere(':tag'.$cpt.' MEMBER OF a.tags')
            ->setParameter('tag'.$cpt, $tag);
        $cpt++;
    }
}
```
Nous avons besoin d'un foreach en mettant les ```$cpt```, sinon le champ tag serait toujours écrasé par le suivant.

Notre système de filtre devrait fonctionner avec les tags !

Il ne reste plus qu'à afficher un template avec les résultats de recherche. Mais je te laisse faire, tu as toutes les compétences requises pour y arriver.

![SKILLS](https://media.giphy.com/media/9541eIHk1MNLa/giphy.gif)

---

---

---

---

----

---
Bon voici ma proposition de réponse si tu ne t'en sors pas, on va juste réutiliser le template qui permet d'afficher toutes les annonces:
```php
<?php

namespace App\Controller;

use App\Entity\AnnonceSearch;
use App\Form\AnnonceSearchType;
use App\Repository\AnnonceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceSearchController extends AbstractController
{
    #[Route('/annonce/search', name: 'app_annonce_search', methods: ['GET'])]
    public function index(Request $request, AnnonceRepository $annonceRepository, PaginatorInterface $paginator): Response
    {
        $annonceSearch = new AnnonceSearch();
        $form = $this->createForm(AnnonceSearchType::class, $annonceSearch);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonces = $paginator->paginate(
                $annonceRepository->findByAnnonceSearchQuery($annonceSearch),
                $request->query->getInt('page', 1),
                12
            );

            return $this->render('annonce/index.html.twig', [
                'annonces' => $annonces
            ]);
        }

        return $this->render('annonce_search/_search-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```