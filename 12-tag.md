# Tag
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).  

L'objectif ici est de pouvoir ajouter des tags aux annonces. Une annonce pourra avoir plusieurs tags et un tag pourra avoir plusieurs annonces.
Pour mettre ce système en place, nous allons avoir besoin de créer une relation entre ces deux entités.

## Rappel sur les relations :
![schema des relations](https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1rrzOny4FT926FF46EAXHR2XRISHaGSuIvzlUIxSKT8Q8Cp_N "schema des relations") 
### ManyToOne n..1
Cette relation spécifie que la base est liée à une seule cible et qu’une cible peut être liée à plusieurs bases.
#### Exemple
Un Tag est lié à une seule Annonce
Une Annonce est liée à plusieurs Tags.

### OneToMany 1..n
Cette relation spécifie que la __base__ peut être liée à plusieurs __cibles__, mais qu’une __cible__ est liée à une seule __base__.
#### Exemple
Un __Tag__ est lié à plusieurs __Annonces__.
Une __Annonce__ est liée à un seul __Tag__.

### ManyToMany n..n
Cette relation spécifie que la __base__ peut être liée à plusieurs __cibles__ et qu’une __cible__ peut être liée à plusieurs __bases__.
#### Exemple
Un __Tag__ est lié à plusieurs __Annonces__.
Une __Annonce__ est liée à plusieurs __Tag__.

### OneToOne 1..1
La relation OneToOne n’est pas la plus utilisée, mais peut être utile dans certains cas.
Cette relation spécifie que la __base__ est liée à une seule __cible__ et qu’inversement une __cible__ est liée à une seule __base__.
#### Exemple
Un __Tag__ est lié à exactement une __Annonce__.
Une __Annonce__ est liée à précisément un __Tag__.

## L'entité Tag
Tu l'auras compris, la relation qui correspond à notre besoin est la relation __ManyToMany__.

Créons sans plus attendre l'entité Tag :
```shell
php bin/console make:entity

Class name of the entity to create or update (e.g. TinyJellybean):
 > Tag

 created: src/Entity/Tag.php
 created: src/Repository/TagRepository.php
 
 Entity generated! Now let s add some fields!
 You can always add more fields later manually or by re-running this command.

 New property name (press <return> to stop adding fields):
 > name

 Field type (enter ? to see all types) [string]:
 > string

 Field length [255]:
 > 

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/Tag.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > annnonces

 Field type (enter ? to see all types) [string]:
 > relation

 What class should this entity be related to?:
 > Annonce

What type of relationship is this?
 ------------ ------------------------------------------------------------------ 
  Type         Description                                                       
 ------------ ------------------------------------------------------------------ 
  ManyToOne    Each Tag relates to (has) one Annonce.                            
               Each Annonce can relate to (can have) many Tag objects            
                                                                                 
  OneToMany    Each Tag can relate to (can have) many Annonce objects.           
               Each Annonce relates to (has) one Tag                             
                                                                                 
  ManyToMany   Each Tag can relate to (can have) many Annonce objects.           
               Each Annonce can also relate to (can also have) many Tag objects  
                                                                                 
  OneToOne     Each Tag relates to (has) exactly one Annonce.                    
               Each Annonce also relates to (has) exactly one Tag.               
 ------------ ------------------------------------------------------------------ 

 Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
 > ManyToMany

 Do you want to add a new property to Annonce so that you can access/update Tag objects from it - e.g. $annnonce->getTags()? (yes/no) [yes]:
 > yes

 A new property will also be added to the Annonce class so that you can access the related Tag objects from it.

 New field name inside Annonce [tags]:
 > 

 updated: src/Entity/Tag.php
 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > 


           
  Success! 
           

 Next: When you're ready, create a migration with make:migration
```
N'hésite pas à aller voir ta nouvelle entité __src/Entity/Tag.php__ :
```php
<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Annonce::class, inversedBy: 'tags')]
    private Collection $annonces;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        $this->annonces->removeElement($annonce);

        return $this;
    }
}
```
Et va voir ton entité __Annonce__ :
```php
<?php

class Annonce
{
    //...

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'annonces')]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    //...

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addAnnonce($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeAnnonce($this);
        }

        return $this;
    }
}
```

N'oublie pas de procéder à la mise à jour de la base de données !
```shell
php bin/console make:migrations
php bin/console doctrine:migration:migrate
```

## CRUD (Create Read Update Delete)
À la manière de ```php bin/console make:controller``` Symfony nous permet de générer un système de [CRUD](https://fr.wikipedia.org/wiki/CRUD) pour une entité.
Pour cela, il te suffit de taper :
```shell
php bin/console make:crud Tag
```
Automatiquement, Symfony créé le controller __src/Controller/TagController.php__, son Repository __src/Repository/TagRepository.php__, le FormType __src/Form/TagType.php__, et les templates liés :
- templates/tag/_delete_form.html.twig
- templates/tag/_form.html.twig
- templates/tag/edit.html.twig
- templates/tag/index.html.twig
- templates/tag/new.html.twig
- templates/tag/show.html.twig

Tu peux aller voir le résultat grâce au chemin spécifié dans le controller : __/tag__.  
En une seule ligne de commande, Symfony à généré tout le système de gestion de tag ! Alors que nous avons tout fait à la main jusqu'à maintenant !
Maintenant, à nous de l'adapter à nos besoins !

Si tu essaies de créer un tag, tu devrais avoir une erreur. 
Pour la corriger, il faut éditer __src/Form/TagType.php__
```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('name')
        //->add('annonce') // c'est une Entité et non un type de champ. Symfony ne sait pas comment afficher cette entitié dans le formulaire html, d'où l'erreur
    ;
}
```
Essaies de créer quelques tags !

## Association des Tags
Lors de la création d'une annonce, il serait logique d'ajouter un champ dans le formulaire, permettant de choisir un tag.  

### Exercice
Pour ce faire, je te laisse essayer d'ajouter un champ de type __ChoiceType__ dans le formulaire de création d'une annonce.  
Comme toujours, [la documentation pourra t'aider](https://symfony.com/doc/current/reference/forms/types/entity.html) ;).

---
---
---
![later](https://i.ytimg.com/vi/_8KGlebWqTQ/maxresdefault.jpg)

--- 
---
---
#### Correction
Pour associer des Tags à une annonce, tu peux modifier le formulaire d'édition d'annonce __src/Form/AnnonceType.php__ : 
```php
->add('tags', EntityType::class, [
    'class' => Tag::class,
    'choice_label' => 'name', // c'est la propriété dans l'entitié Tag qui sera affichée dans le select
    'multiple' => true
])
```
Tu peux maintenant essayer d'associer des Tags à une annonce et vérifier en base de données, dans la table __tag_annonce__.
Il semble que ça ne soit pas prit en compte, aucune entrée n'est ajoutée à cette table !

### Notion de propriétaire et d'inverse à la rescousse !
La notion de propriétaire et d'inverse est abstraite, mais importante à comprendre. 
Dans une relation entre deux entités, il y a toujours une entité dite __propriétaire__ et une dite __inverse__. 
L'entité propriétaire et l'entité qui est __responsable__ de la relation. [Voir la documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association)

C'est donc l'entitié Annonce qui devrait être le __owning side__, et non l'entité Tag. 
En effet, on va plus souvent faire ```$annonces->getTags()``` plutôt que l'inverse.  
Ceci permettra aussi que lorsqu'on enregistre une annonce, celle-ci enregistrera aussi la liaison.

Pour se faire, tu peux modifier __src/Entity/Annonce.php__ :
```php
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'annonces')]
    private Collection $tags;
```
et dans __src/Entity/Tag.php__ :
```php
    #[ORM\ManyToMany(targetEntity: Annonce::class, mappedBy: 'tags')]
    private Collection $annonces;
```
```inversedBy``` signifie : je suis l'entité __propriétaire__ et mon entité _inverse_ est...
```mappedBy``` signifie : je l'entité __inverse__ et mon entité __propriétaire__ est...

Si tu essaies d'éditer une annonce, tu as une erreur car la table pivot (intérmédiaire) n'est plus correcte. 
Effectivement, elle devrait se nommer __annonce_tag__ et non __tag_annonce__ maintenant (c'est l'entité propriétaire qui est en premier).

### RollBack
Ce n'est pas grave, ça arrive de faire des erreurs de conception ! Mais pas de panique, grâce aux migrations, nous pouvons revenir en arrière.  
Nous allons donc changer cela en base de données en procédant à un rollback vers une version antérieure à la base de données actuelle. 
Pour cela :
```shell
php bin/console doctrine:migrations:status

+----------------------+----------------------+------------------------------------------------------------------------+
| Configuration                                                                                                        |
+----------------------+----------------------+------------------------------------------------------------------------+
| Storage              | Type                 | Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration |
|                      | Table Name           | doctrine_migration_versions                                            |
|                      | Column Name          | version                                                                |
|----------------------------------------------------------------------------------------------------------------------|
| Database             | Driver               | Symfony\Bridge\Doctrine\Middleware\Debug\Driver                        |
|                      | Name                 | main                                                                   |
|----------------------------------------------------------------------------------------------------------------------|
| Versions             | Previous             | DoctrineMigrations\Version20221211164244                               |
|                      | Current              | DoctrineMigrations\Version20221212085821                               |
|                      | Next                 | Already at latest version                                              |
|                      | Latest               | DoctrineMigrations\Version20221212085821                               |
|----------------------------------------------------------------------------------------------------------------------|
|                      | Available            | 9                                                                      |
|                      | New                  | 0                                                                      |
|----------------------------------------------------------------------------------------------------------------------|
| Migration Namespaces | DoctrineMigrations   | /home/vodoo/devenv/cours-symfo-v2/migrations                           |
+----------------------+----------------------+------------------------------------------------------------------------+
```
Ce qui nous intéresse se situe à la ligne ```Versions             | Previous             | DoctrineMigrations\Version20221211164244 ```: c'est la migration vers laquelle nous voulons aller pour revenir en arrière.

Tu peux revenir à cette version en faisant :
```shell
php bin/console doctrine:migrations:migrate DoctrineMigrations\\Version20221211164244
```

Tu peux donc supprimer ta dernière migration (en vérifiant bien que c'est celle qui est concerné !) et en générer une nouvelle :
```shell
php bin/console make:migration
```
Puis, si le fichier est correcte :
```shell
php bin/console doctrine:migrations:migrate
```
Ajoute quelques tags et essaies de créer une annonce ! Normalement tout fonctionne !

## Cosmétiques des select
Pour rendre les ```select``` un peu plus sympa nous pouvons utiliser la librairie [select2](https://cdnjs.com/libraries/select2).
Il suffit d'ajouter la librarie et ce code dans le template __templates/base.html.twig__: 
```php
<!DOCTYPE html>
<html>
    <head>
        {# ... #}
        {% block stylesheets %}
            {# ... #}
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        {% endblock %}
    </head>
    <body>
        {% block body %}{% endblock %}

        {% block javascripts %}
            {# ... #}
            <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script>$('select').select2()</script>
        {% endblock %}
    </body>
</html>
```
> Note que nous le block __javascripts__ a été déplacé avant la fermeture du body, cela permet d'utiliser Javascript seulement lorsque le DOM est chargé.  
> Tu peux aussi te dire que ce n'est pas très optimisé car nous chargeons jQuery et Select2 sur toutes les pages, alors que ces librairies sont uniquement nécessaires sur le formulaire annonces, pour le moment. 
> Webpack permettrait de régler le problème simplement. Mais pour l'instant, restons simple, nous aussi, et reportons ce problème à plus tard.

## Authorization
N'hésite pas à mettre en pratique ce que tu as vu dans les parties précédentes pour sécuriser l'application avec les authorizations : on va dire que seuls les __admin__ peuvent gérer les tags.

## Conclusion
Tu sais maintenant créer des relations ManyToMany et un système de CRUD. Bien joué !

![bien joué](https://media.giphy.com/media/aLdiZJmmx4OVW/giphy.gif)