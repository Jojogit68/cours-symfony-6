# Formulaires
> Les documentations seront tes meilleures amies si tu souhaite progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

Dans Symfony, il est possible de créer des formulaires directement liés aux entités.
Ces formulaires seront sous forme de classe _PHP_ et il sera possible de générer les inputs correspondant aux
propriétés de l'entité en quelques lignes : [voir la doc](https://symfony.com/doc/current/forms.html)

![input](https://media.giphy.com/media/PuEbN8C4ttcNa/giphy.gif)

## Édition
### Exercice
Nous allons créer un formulaire pour éditer une annonce. La première étape est de donc de créer une fonction dans un controller
qui répond à une route et qui renvoie un template.
Tu peux donc :
- créer une nouvelle fonction __edit__ dans __AnnonceController__;
- cette fonction répondra à la route __annonce/{id}/edit__ avec un requirement pour l'id, et cette route répondra seulement aux méthodes __GET__ et __POST__;
- récupère l'annonce avec l'id passé en paramètre en base de données, et envoie l'annonce au template (à créer juste ci-dessous);
- créer un nouveau template __templates/annonce/edit.html.twig__ qui affiche ```<h1>Édition de l'annonce {{ annonce.title }}</h1>```;
- ajoutes un lien vers cette nouvelle route dans __templates/annonce/_card.html.twig__, au niveau du lien 'éditer';

---
---
---
![Later](https://i.ytimg.com/vi/Uqn7V-Fjjq0/maxresdefault.jpg)

---
---
---

#### Correction
Ajoutons une nouvelle fonction dans __AnnonceController __:
```php
    // reste du code ...
    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Annonce $annonce): Response
    {
        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce
        ]);
    }
```

Et le template __templates/annonce/edit.html.twig__ :
```html
{% extends '_layout/full-width.html.twig' %}

{% block title %}Edition de l'annonce {{ annonce.id }}-{{ annonce.title }}{% endblock %}
{% block content %}
    <h1>Edition de l'annonce {{ annonce.title }}</h1>
{% endblock %}
```
Le lien dans __templates/annonce/_card.html.twig__ :
```html
<a class="btn btn-sm btn-outline-secondary" type="button" href="{{ path('app_annonce_edit', {id: annonce.id}) }}">Edit</a>
```

### Création d'un formulaire dans un Controller
Dans la fonction __edit__, voici tu peux ajouter le code suivant, qui permet de créer un formulaire façon Symfony:
```php
    // rest du code...
    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Annonce $annonce): Response
    {
        $formBuilder = $this->createFormBuilder(); // on crée un objet FormBuilder qui permet de construire le formulaire
        $formBuilder->add('title'); // on ajoute des champs
        $formBuilder->add('description', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class); // on peut changer le type de champs (on aurait pu utiliser un use, mais pour plus de simplicité, j'ai écrit l'espace de nom directement)
        $form = $formBuilder->getForm(); // on utilise la fonction getForm afin que le FormBuilder nous renvoie un objet de type FormInterface
        $formView = $form->createView(); // grâce à cet objet FormInterface, on peut construire la vue Twig avec createView

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'formView' => $formView
        ]);
    }
```
Pour afficher le formulaire, c'est tout simple : on utilise la fonction Twig ```form()``` dans le template __templates/annonce/edit.html.twig__ :
```html
{% extends '_layout/full-width.html.twig' %}

{% block title %}Edition de l'annonce {{ annonce.id }}-{{ annonce.title }}{% endblock %}
{% block content %}
    <h1>Edition de l'annonce {{ annonce.title }}</h1>
    {{ form(formView) }} 
{% endblock %}
```

Actualise la page d'édition pour voir le résultat ! Tu devrais voir un formulaire avec un champ texte et un champ textarea
et tout ça sans avoir écrit une ligne de HTML !

Pour remplir le formulaire avec les données de l'annonce récupérée en base de données, il suffit de modifier un tout petit peut le code:
```php
    // reste du code...
    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Annonce $annonce): Response
    {
        $formBuilder = $this->createFormBuilder($annonce);
```

Actualise la page ! Les champs du formulaire sont remplis avec les données de l'annonce !

Nous savons donc comment construire un formulaire, parfait. Mais dans la logique de séparation des rôles, il faudrait
que la construction ce formulaire soit dans un fichier à part, au cas où nous voudrions le réutiliser ailleurs !
Et ça tombe bien ! Symfony nous propose une solution !

### Exercice
Objectif : éditer une annonce.  
En suivant [la doc sur les formulaires](https://symfony.com/doc/current/forms.html), essaye de :
- Créer une classe de formulaire (un FormType) qui sera mapper sur l'entité __Annonce__ ;
- grâce à la fonction __edit__, affiche ce formulaire dans le template __templates/annonce/edit.html.twig__ ;
- lors de l'envoi du formulaire, toujours dans la fonction __edit__, met à jour l'annonce avec les informations envoyées dans le formulaire.
> Tout est dans la DOC ! Prends quelques minutes pour essayer tout seul et te familiariser avec la documentation de Symfony, ça sera très important dans le futur
---
---
---
![later](https://i.ytimg.com/vi/U7CZcd-UYmU/maxresdefault.jpg)

---
---
---

#### Correction

##### Création et affichage
Pour créer le formulaire, il faut commencer par créer une classe __Type__. Les __Types__ sont des classes php dont le
nom commence par le nom de l'entité pour laquelle tu veux créer un formulaire et est suffixé par __Type__
(en général, rien ne t'empêche de changer le nom selon tes besoins).
``` console
php bin/console make:form
```
Et répondre de la manière suivante :
``` console
The name of the form class (e.g. OrangePuppyType):
> AnnonceType

The name of Entity or fully qualified model class name that the new form will be bound to (empty for none):
> Annonce

created: src/Form/AnnonceType.php


    Success! 


Next: Add fields to your form and start using it.
Find the documentation at https://symfony.com/doc/current/forms.html
```
On se retrouve avec un nouveau fichier __src/Form/AnnonceType.php__ que tu peux ouvrir.

```php
# src/Form/AnnonceType.php
<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('price')
            ->add('status')
            ->add('isSold')
            ->add('createdAt')
            ->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}

```

Pour afficher le formulaire, modifie __src/Controller/AnnonceController.php__ :
```php
/**
 * @Route("/annonce/{id}/edit")
 */
public function edit(Annonce $annonce)
{
    $form = $this->createForm(AnnonceType::class, $annonce); // si on ne met pas le 2e paramètre $annonce, le formulaire ne sera pas pré-remplie
    // il ne sera pas non plus lié à l'instance de l'entité Annonce qui vient de la base de donnée ! Et l'enregistrement ne sera pas automatique (on voit ça après)

    return $this->render('annonce/edit.html.twig', [
        'annonce' => $annonce,
        'form' => $form->createView() // comme le code précédent, sauf qu'on utilise la classe AnnonceType
    ]);
}
```
Et modifie __templates/annonce/edit.html.twig__ :
```html
{% block content %}
    <div class="container">
        <h1>Éditer l'annonce</h1>
        {{ form_start(formView) }} {# cette fonction permet d'ouvrir la balise HTML form #}
            {{ form_widget(formView) }} {# cette fonction permet d'afficher le reste du formulaire #}
            <button class="btn btn-primary">Sauvegarder</button>
        {{ form_end(formView) }} {# cette fonction permet de fermer la balise HTML form #}
    </div>
{% endblock %}
```

> Tu peux [changer le thème](https://symfony.com/doc/current/form/form_themes.html) des formulaires dans __config/packages/twig.yaml__ et ajouter
```yaml
twig:
  default_path: '%kernel.project_dir%/templates'
  form_themes: ['bootstrap_5_horizontal_layout.html.twig'] # ligne à ajouter
```

Pour [customiser le formulaire](https://symfony.com/doc/current/form/form_customization.html) (c'est aussi dans la doc !), il est aussi possible de séparer les différents champs :
```html
<div class="col-md-4">
    {{ form_row(form.title) }}
</div>
<div class="col-md-4">
    {{ form_row(form.description) }}
</div>
<div class="col-md-4">
    {{ form_row(form.description) }}
</div>
```

##### Soumission du formulaire
Pour enregistrer un changement sur une entité, tu as besoin de l'EntityManager de Doctrine.
Tu peux soit le récupérer en injectant ManagerRegistry dans ta méthode
```php
use Doctrine\Persistence\ManagerRegistry;
    //...
    public function edit(ManagerRegistry $doctrine) 
    {
        $em = $doctrine->getManager();
    }
```  
Soit injecter directement EntityManagerInterface, qui instanciera directement le manager.  
Dans __src/Controller/Admin/AnnonceController.php__ :
```php
# ne pas oublier ces use
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
# ...
    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request); // on dit au formulaire d'écouter la requête

        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est envoyé et s'il est valide
            $em->flush();
            return $this->redirectToRoute('app_annonce_index');
        }
            
        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }
```
Essaies de soumettre le formulaire et admire le résultat !

#### WUT ??
![WUT ?](https://media.giphy.com/media/11HdhZBMpdxClG/giphy.gif)

Comment l'annonce a-t-elle pu se mettre à jour, alors qu'on ne l'hydrate pas avec les informations que l'on reçoit du formulaire ?
```php
// hydrater c'est :
$annonce->setTitle('Titre de l\'annonce');
$annonce->setDescription('Description...');
```

ou encore :
```php
$annonce->setTitle($_POST['title']);
$annonce->setDescription($_POST['description']);
// etc...
```

Ce qui est important ici ce sont ces deux lignes
```php
$form = $this->createForm(AnnonceType::class, $annonce);
$form->handleRequest($request);
```

Grâce à ```$this->createForm(AnnonceType::class, $annonce);$this->createForm(AnnonceType::class, $annonce);```, Symfony sait que le formulaire est _mappé_ sur l'instance de __Annonce__, il connaît ses propriétés.

```$form->handleRequest($request);``` permet quant à elle d'hydrater l'objet avec les informations passé en POST.
### Exercice
Tu te souviens des événements Doctrine ???

![Remember ?](https://media.giphy.com/media/3ohjUNm1c3oMRTcyoU/giphy.gif)

Le but est d'ajouter un champ ```updatedAt``` à l'entité annonce, de manière à savoir quand est ce qu'une annonce a été éditée.
- ajoute une propriété ```updatedAt``` au modèle __Annonce__ ;
- en suivant la [documentation](https://symfony.com/doc/current/doctrine/events.html), mets à jour le champ ```updatedAt``` avant chaque mise à jour ;
- essaies d'éditer une annonce et vérifie que le champ ```updatedAt``` est bien mis à jour à chaque édition.

---
---
---
![Later](https://i.ytimg.com/vi/TxSrZFB9AT0/hqdefault.jpg)

---
---
---
#### Correction
```php
# src/Entity/Annonce.php
    // reste du code ...
    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->slug = (new Slugify())->slugify($this->title);
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // reste du code ...
    #[ORM\Column(options: [
        'default' => 'CURRENT_TIMESTAMP' // c'est pour mettre une valeur par défaut, sinon la migration ne fonctionnera pas
    ])]
    private ?\DateTimeImmutable $updatedAt = null;
```

## Options de formulaire
Grâce aux options de formulaire, on va pouvoir, par exemple, conditionner l'affichage des champs selon le rôle de l'utilisateur, ou encore spécifier si un champ est requis ou non, donner une valeur à un champ, etc...

N'hésite pas à lire la [doc à ce sujet](https://symfony.com/doc/current/forms.html#other-common-form-features), cela nous servira plus tard pour afficher ou non des champs selon que l'utilisateur est admin ou non.

### Les labels
Pour changer les labels du formulaire tu peux passer par le [système de traduction](https://symfony.com/doc/current/translation.html) :
```php
$builder
    ->add('title', null, ['label' => 'title'])
    ->add('description', null, ['label' => 'description'])
    ->add('price', null, ['label' => 'price'])
    ->add('status', null, ['label' => 'status'])
    ->add('isSold', null, ['label' => 'sold'])
    ->add('createdAt', null, ['label' => 'created_at'])
    ->add('slug', null, ['label' => 'slug'])
;
```
- Change la variable _default_locale_ dans __config/packages/translation.yaml__ :
```yaml
framework:
  default_locale: fr
```
- Et ajoute le fichier suivant __translations/messages.fr.yaml__ :
``` yaml
title: Titre
description: Description
price: Prix
status: Êtat
sold: Vendu
created_at: Créé le
slug: URL SEO
```
Tes labels sont maintenant traduits !

## Les type de champ
Le champ __status__ serait plus adapté à un champ de type select. L'idée serait d'avoir un champ select avec les choix :
- Très mauvais ;
- Mauvais ;
- Bon ;
- Très bon ;
- Parfait.

### Exercice
En te référant à la [doc](https://symfony.com/doc/current/reference/forms/types.html), change les types de champs :
- __status__ en type _ChoiceType_ ;
- __createdAt__ en type _DateType_, afin que le champ se présente sous forme de datepicker et non plus en select ;
- tu peux aussi (ce n'est pas obligé) changer le champ description en y intégrant un _wysiwyg_ avec la librairie [Trumbowy](ghttps://alex-d.github.io/Trumbowyg/) par exemple.

---
---
---
![later](https://i.ytimg.com/vi/9jY4d6mGAUA/maxresdefault.jpg)

---
---
---

#### Correction
Dans __src/Form/AnnonceType.php__ :
```php
# ne pas oublier
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
// ...
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('title', null, ['label' => 'title'])
        ->add('description', null, ['label' => 'description'])
        ->add('price', null, ['label' => 'price'])
        ->add('status', ChoiceType::class, [
            'label' => 'status.word',
            'choices' => [
                'status.very_bad' => Annonce::STATUS_VERY_BAD,
                'status.bad' => Annonce::STATUS_BAD,
                'status.good' => Annonce::STATUS_GOOD,
                'status.very_good' => Annonce::STATUS_VERY_GOOD,
                'status.perfect' => Annonce::STATUS_PERFECT
            ]
        ])
        ->add('isSold', null, ['label' => 'sold'])
        ->add('createdAt', DateType::class, [
            'label' => 'created_at',
            'widget' => 'single_text',
            'input'  => 'datetime_immutable'
        ])
        ->add('slug', null, ['label' => 'slug'])
    ;
}
```

Et voici le fichier de traductions __translations/messages.fr.yaml__ :
```yaml
title: Titre
description: Description
price: Prix
status:
    word: Êtat
    very_bad: Très mauvais
    bad: Mauvais
    good: Bon
    very_good: Très bon
    perfect: Parfait
sold: Vendu
created_at: Créé le
slug: URL SEO
```

## Création
Tu te souviens de la méthode ```new``` du contrôleur __src/Controller/AnnonceController.php__ ?
![mouhaha](https://media.giphy.com/media/3o6nV1ouOsNLBTEqQM/giphy.gif)

Nous avions hydraté l'entité __Annonce__ nous même, mais
maintenant que tu sais comment afficher un formulaire pour éditer une annonce, la création ne devrait pas poser de problème.

### Exercice
L'objectif dans cette méthode est d'afficher et d'enregistrer une nouvelle annonce via le formulaire.

Cette fois, je ne mets pas les étapes. Je te laisse essayer, mais n'oublie pas te référer à la [doc](https://symfony.com/doc/current/forms.html) !

---
---
---
![later](https://i.ytimg.com/vi/LKSLjuMtmzQ/maxresdefault.jpg)

---
---
---

#### Correction

Dans __src/Controller/AnnonceController.php__ :
```php
public function new(Request $request, EntityManagerInterface $em)
{
    $annonce = new Annonce();

    $form = $this->createForm(AnnonceType::class, $annonce);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($annonce);
        $em->flush();
        return $this->redirectToRoute('app_annonce_index');
    }
        
    return $this->render('annonce/new.html.twig', [
        'annonce' => $annonce,
        'form' => $form->createView()
    ]);
}
```
Le formulaire sera commun aux vues édition et création, tu peux donc faire un template commun. Crées le template __templates/annonce/_form.html.twig__ :
```html
{{ form_start(form) }}
{{ form_widget(form) }}
    <button class="btn btn-primary">{{ button|default('save'|trans) }}</button>
{{ form_end(form) }}
```

Crées un template __templates/annonce/new.html.twig__ :
```html
{% extends "_layout/sidebar.html.twig" %}
{% block title %}Nouvelle annonce!{% endblock %}
{% block content %}
    <h1>Nouvelle annonce</h1>
    {{ include("annonce/_form.html.twig", {form: formView, button: 'create'|trans}) }}
{% endblock %}

```

Tu peux aussi modifier le template __templates/annonce/edit.html.twig__ :
```html
{% extends "_layout/sidebar.html.twig" %}
{% block title %}Éditer l'annonce!{% endblock %}
{% block content %}
    <h1>Éditer l'annonce</h1>
    {{ include("annonce/_form.html.twig", {form: formView, button: 'edit'|trans}) }}
{% endblock %}
```

Comme tu peux le voir, j'ai utilisé un filtre Twig pour traduire les boutons ```str|trans```. Donc dans __translations/messages.fr.yaml__
```yaml
#...
create: Créer
save: Sauvegarder
edit: Éditer
```

Ton formulaire de création est normalement fonctionnel !

## Suppression
Dans le controller __src/Controller/AnnonceController.php__, ajoute cette méthode:

```php
#[Route('/annonce/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
public function delete(Annonce $annonce, EntityManagerInterface $em)
{
    // on supprime l'annonce de l'ObjetManager
    $em->remove($annonce);
    // en envoie la requête en base de données
    $em->flush();
    return $this->redirectToRoute('app_annonce_index');
}
```
On peut remarquer qu'on demande à la route de ne répondre qu'à la méthode DELETE.
En HTML, il n'est pas possible de spécifier cette méthode pour un formulaire, mais Symfony nous propose un petit "Hack".

Dans __templates/annonce/_card.html.twig__, ajoute ce petit formulaire:
```html
{# ... #}
<form method="post" action="{{ path('app_annonce_delete', {id: annonce.id}) }}">
  <input type="hidden" name="_method" value="DELETE">
  <button class="btn btn-danger">Supprimer</button>
</form>
```
Tu peux remarquer l'input hidden et son ```name``` qui permet à Symfony de simuler l'envoie d'une requête DELETE.

Dans les versions supérieures à Symfony 4.4, pour que cela fonctionne, il faut aussi changer la valeur de ```http_method_override``` dans la configuration de ```config/packages/framework.yaml```:
```yaml
# see https://symfony.com/doc/current/reference/configuration/framework.html
framework:
    secret: '%env(APP_SECRET)%'
    http_method_override: true
```

Essaye de supprimer quelque chose en allant sur la route de suppression ! Tu devrais voir s'afficher __suppression__.

## Sécurité CSRF
Notre formulaire de suppression n'est pas sécurisé, une personne mal intentionnée peut tout à fait trouver et injecter ce formulaire sur le site et ainsi donner la possibilité à d'autres utilisateurs de supprimer des annonces sans même qu'ils s'en aperçoivent !

Nous allons donc utiliser un jeton [csrf](https://fr.wikipedia.org/wiki/Cross-site_request_forgery).

Si tu regardes tes autres formulaires, Symfony les a générés pour toi et il s'occupe de faire la vérification lui-même.

Ici, il s'agit d'un formulaire custom, c'est donc toi qui vas devoir générer et vérifier ce jeton.

Dans __templates/admin/annonce/index.html.twig__ :
```html
<form method="post" action="{{ path('app_annonce_delete', {id: annonce.id}) }}">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ annonce.id) }}">
    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
</form>
```
Et dans __src/Controller/Admin/AnnonceController.php__ :
```php
/**
 * @Route("/annonce/{id}", methods="DELETE")
 */
public function delete(Annonce $annonce, EntityManagerInterface $em, Request $request)
{
    if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->get('_token'))) {
        $em->remove($annonce);
        $em->flush();
    }
    return $this->redirectToRoute('app_annonce_index');
}
```
Tu peux vérifier que cela fonctionne en modifiant le jeton dans l'inspecteur de ton navigateur.

Tu peux aussi ajouter un message de validation avant suppression
```html
<form method="post" action="{{ path('app_admin_annonce_delete', {id: annonce.id}) }}" onsubmit="return confirm('Êtes vous vraiment sûr ?')">
```

## Message Flash
Les messages Flash vont te permettre d'afficher des messages en utilisant la session PHP.

### Exercice
Grâce à la [doc](https://symfony.com/doc/current/controller.html#flash-messages) (encore elle...),
essaie d'afficher un message de succès si une annonce est bien créée, éditée, supprimée.

#### Correction

---
---
---
![later](https://i.ytimg.com/vi/KUro66ItaBo/maxresdefault.jpg)

---
---
---

Dans __src/Controller/AnnonceController.php__ :
```php
public function edit(Annonce $annonce, EntityManagerInterface $em, Request $request)
{
    $form = $this->createForm(AnnonceType::class, $annonce);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        // ajout du message flash
        $this->addFlash('success', 'Annonce modifiée avec succès');
        return $this->redirectToRoute('app_annonce_index');
    }
```

Dans __templates/annonce/index.html.twig__ :
```php
{# reste du code ... #}
{% block content %}
    <div class="container">
        {% for message in app.flashes('success') %}
            <div class="alert alert-success">
                {{ message }}
            </div>
        {% endfor %}
{# reste du code ... #}
```

## Administration
Nous allons créer un espace permettant d'administrer les annonces. Dans cet espace, un administrateur pourra
- voir toutes les annonces,
- éditer une annonce,
- supprimer une annonce

Crée un contrôleur qui s'occupera de la partie administration des annonces :
```shell
php bin/console make:controller Admin\\Annonce
```
Les fichiers __src/Controller/Admin/AnnonceController.php__ et __templates/admin/annonce/index.html.twig__ ont dû être généré.

### Exercices
- Dans le controller généré, dans la fonction __index__, récupère toutes les annonces depuis la base données ;
- renvoie toutes les annonces au template ;
- affiche les annonces dans le template sous forme de tableau avec au moins
    - une colonne titre ;
    - une colonne action avec
        - un bouton __Éditer__ avec un lien vers l'édition ;
        - un bouton __Supprimer__ avec un lien vers la suppression ;
        - un bouton __Voir__ pour voir l'annonce
---
---
---
![later](https://i.ytimg.com/vi/mVKN3Lrir2o/maxresdefault.jpg)

---
---
---

#### Correction
Dans __src/Controller/Admin/AnnonceController.php__ :
```php
<?php

namespace App\Controller\Admin;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AnnonceController extends AbstractController
{
    #[Route('/annonce')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll();
        return $this->render('admin/annonce/index.html.twig', [
            'annonces' => $annonces
        ]);
    }
}
```

Dans __templates/admin/annonce/index.html.twig__:
```php
{% extends '_layout/sidebar.html.twig' %}
{% block title %}Gérer les annonces!{% endblock %}
{% block content %}
    <div class="container">
        <h1>Gérer les annonces</h1>
        <div class="row">
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
                        <td>{{ annonce.title }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ path('app_annonce_show', {id: annonce.id, slug: annonce.slug}) }}" class="btn btn-outline-secondary">Voir</a>
                                <a href="{{ path('app_annonce_edit', {id: annonce.id}) }}" class="btn btn-outline-secondary">Éditer</a>
                                <form method="post" action="{{ path('app_annonce_delete', {id: annonce.id}) }}" onsubmit="return confirm('Êtes vous vraiment sûr ?')">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ annonce.id) }}">
                                    <button class="btn btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
{% endblock %}
```

Pour faire fonctionner le bouton Éditer, il nous faut créer une route et sa méthode.
---
You're done ! Ce n'était pas facile. Bravo pour avoir fini cette partie !   
![Bravo](https://media.giphy.com/media/TgFibAJdezKXUYsddv/giphy.gif)
