# Validation des entités
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

Par défaut, Symfony choisit les types des champs des formulaires grâce au type de propriété des entités.
Exemple avec le champ _title_ qui est une propriété de type _string_ dans l'entité __Annonce__, le champ du formulaire correspondant sera de type _text_.

Les validations permettront d'être sûr que les informations enregistrées en base de données sont bien valides,
et ce, peu importe comment l'entité est enregistrée :
- depuis un controller ;
- depuis un formulaire ;
- depuis les fixtures ;
- depuis la console ;
- depuis une API ;
- etc...
  Elles sont donc très utiles pour maintenir l'intégrité de la base de données !

## Utilisation
Pour l'exemple, nous allons vérifier que la description d'une annonce n'est pas trop courte.

La première chose à faire est de se rendre sur la [documentation](https://symfony.com/doc/current/validation.html) pour voir comment utiliser les validations.

Dans la documentation, on voit que l'on peut utiliser [Length](https://symfony.com/doc/current/reference/constraints/Length.html), c'est donc ce que nous allons faire.

Modifie ton entité __src/Entity/Annonce.php__ :
```php
// reste du code...
use Symfony\Component\Validator\Constraints as Assert; // ne pas oublier ce use

// reste du code...

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        min: 40,
        minMessage: "La description doit faire plus de {{ limit }} caractères",
    )]
    private ?string $description = null;
```
Si tu essaies de soumettre le formulaire avec une description trop courte, cela ne fonctionnera pas et tu auras normalement un message d'erreur dans la vue.

Tu peux aussi faire qu'un champ soit unique avec [UniqueEntity](https://symfony.com/doc/current/reference/constraints/UniqueEntity.html) :
```php
// reste du code...
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; // ne pas oublier ce use
// reste du code...
#[ORM\Entity]
#[UniqueEntity('slug')]
class Annonce
```

### Exercice
- Ajoute une propriété __imageUrl__ à l'entité __Annonce__ qui sera de type string, 255 caractères et nullable ;
- Procède à la mise à jour de la base de données ;
- Ajoute le champ __imageUrl__ aux formulaires concernés de création et édition ;
- Vérifie que ce champ est bien une URL lors de la soumission des formulaires, et que le protocole de l'url est bien du _https_.

---
---
---
![Later](https://i.ytimg.com/vi/-gNHMog4iHw/maxresdefault.jpg)

---
---
---

#### Correction
Pour l'exemple tu peux ajouter une propriété __imageUrl__ à ton entité __Annonce__ :
```shell
php bin/console make:entity Annonce
```

```shell
Your entity already exists! So let s add some new fields!

 New property name (press <return> to stop adding fields):
 > imageUrl

 Field type (enter ? to see all types) [string]:
 > string

 Field length [255]:
 > 

 Can this field be null in the database (nullable) (yes/no) [no]:
 > yes

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > 


           
  Success! 
           

 Next: When you re ready, create a migration with make:migration
 
```
Mise à jour de la base de données :
```shell
php bin/console doctrine:schema:update --force
```

Et n'oublie pas d'ajouter ce champ à tes formulaires dans __src/Form/AnnonceType.php__ :
```php
$builder
    //...
    ->add('imageUrl', null, ['label' => 'image'])
```
Si tu tentes de modifier une annonce, tu te rends compte que tu peux mettre ce que tu veux dans ce champ, alors qu'il faudrait avoir une url valide.

Modifie ton entité __src/Entity/Annonce.php__ :
```php
    // reste du code...
    use Symfony\Component\Validator\Constraints as Assert; // ne pas oublier ce use
    
    // reste du code...

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(
        protocols: ['https'],
    )]
    private ?string $imageUrl = null;
```

### Exercice
- Dans les différents templates, affiches l'image de l'annonce grâce à la balise ```<img src="url de l'image">```.

---
---
---
![Later](https://i.ytimg.com/vi/wiHYx9NX4DM/maxresdefault.jpg)

---
---
---

#### Correction
```html
<img
    class="img-fluid card-img-top"
    alt="Plastic Duck {{ key }}"
    src="
        {% if annonce.imageUrl is not null %}
            {{ annonce.imageUrl }}
        {% else %}
            https://via.placeholder.com/400?text=Plastic Duck {{ key }}
        {% endif %}"
>
```

N'hésite pas à regarder les différentes validations possibles sur la [documentation](https://symfony.com/doc/current/validation.html) et à compléter tes entités en fonction de ces possibilités. Okay ?

![OK](https://media.giphy.com/media/R459x856IfF6w/giphy.gif)

