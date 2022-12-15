# Aller plus loin
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

Tout ce que nous avons vu peut déjà te permettre de créer des applications.
Mais ce n'est qu'une petite partie de la surface immergée de l'iceberg. En effet, Symfony permet d'aller bien plus loin !

Ci dessous, tu trouveras une liste des fonctionnalités que tu pourrais ajouter (ou non) à ton application.
Je n'irais pas dans le détail, ce serait bien trop long.

> Il n'y a pas de secret, pour créer de nouvelles fonctionnalités qui sortent de que nous avons pu voir dans le cours, il va falloir que tu t'aides de la documentation et d'autres ressources (forum, groupes d'entraîde, blog, tutos, vidéeos, etc...).

## Upload d'images
Le but ici serait d'ajouter une fonctionnalité d'upload d'image pour les annonces.

Tu peux soit le faire à la main, en suivant la [documentation](https://symfony.com/doc/current/controller/upload_file.html).

Ou utiliser le package [VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle) comme Symfony le préconise.

### À la main
Il suffit de suivre la procédure indiquer dans la documentation, mais attention, il y aura une petite subtilité !
Au lieu d'ajouter une seule propriété __image__, il faut ajouter deux propriétés :
```php
    # src/Entity/Annonce.php
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagePath;

    /**
     * @Assert\NotBlank(message="Please, upload the product brochure as a PDF file.")
     * @Assert\File(mimeTypes={ "image/jpeg", "image/jpg" })
     */
    private $imageFile; 
        
    // Avec le setter suivant :
    public function setImageFile($imgFile): self
    {
        $this->imageFile = $imgFile;
        $this->setImagePath($imgFile);

        return $this;
    }
```

Ajout du champ de formulaire :
```php
    #src/Form/AnnonceType.php
    // ...
    ->add('imageFile', FileType::class)
```

## php/bin console
Tu peux créer des commandes à la manière de ```php bin/console ma:supser:commande``` afin de répondre à différentes problématiques.

Par exemple : envoyer un email à tous les utilisateurs de l'application qui ont enregistré des recherches, afin de les prévenir que de nouveaux articles correspondent à leur recherche (à la manière du site www.leboncoin.fr).
Ces emails seraient envoyés à 18h par exemple.

Comment ferais-tu ? Tu peux te poser 5 min (ou 10) et y réfléchir...

![letters](https://media.giphy.com/media/SUAFlhUz4QLew/giphy.gif)

Pour répondre à cette problématique, on peut imaginer un script PHP dans lequel on implémenterait la logique adéquate : chercher les utilisateurs, envoyer les emails, etc..., tout en profitant du framework Symfony.

Ce script pourrait être lancé via ```php``` (et non pas grâce à une requête HTTP comme c'est souvent fait... ce n'est pas une bonne pratique), tous les soirs à 18h.

> Cela serait bien si on pouvait créer une ligne de commande Symfony exécutable via [Cron](https://fr.wikipedia.org/wiki/Cron) ! Comme :
``` console
0 18 * * *      php bin/console app:send-search-email
```

Eh bien, [c'est possible grâce à la création de ligne de commande !](https://symfony.com/doc/current/console.html) !

## Extensions Twig
En plus des dizaines de [filtres et de fonctions](https://twig.symfony.com/doc/2.x/) par défaut définis par Twig, [Symfony définit également certains filtres, fonctions et balises](https://symfony.com/doc/current/reference/twig_reference.html) pour intégrer les différents composants Symfony aux templates Twig.

Une petite [recherche sur packagist](https://packagist.org/?query=twig) permet de voir que quelques librairies existent pour ajouter des fonctionnalités à twig, comme [Twig Extension](https://twig-extensions.readthedocs.io/en/latest).

Mais si ce n'est pas encore assez pour toi, tu peux aussi créer [tes propres extensions !](https://symfony.com/doc/current/templating/twig_extension.html)

## Encore
Pour gérer au mieux les ressources publiques (CSS, JS, images, etc...), Symfony met à disposition l'outil [__Encore__](https://symfony.com/doc/current/frontend.html) qui fonctionne avec [Webpack](https://webpack.js.org/).

Si tu souhaites débuter avec Webpack et savoir à quoi cela sert, tu peux suivre [ce tuto](https://www.alsacreations.com/tuto/lire/1754-debuter-avec-webpack.html) :)

## API
Si tu as besoin de créer un WebService, tu peux tout à fait utiliser Symfony.
Tu trouveras pléthore de ressources sur internet pour t'aider.

Mais sache qu'il y a une [librairie officielle](https://api-platform.com/) qui fait le café !  
![coffee](https://media.giphy.com/media/S1JqepRyHq9ag/giphy.gif)

## Mercure
Tu peux aussi créer des systèmes d'événements temps réels (comme un chat) grâce au composant [Mercure](https://symfony.com/doc/current/components/mercure.html).

## Tester son application
Les [tests](https://fr.wikipedia.org/wiki/Test_(informatique)) vont permettre de tester l'application de façon automatiser afin de vérifier qu'un développement ne fait pas régresser l'application.  
Je ne rentrerais pas dans les détails, mais si tu n'es pas familié·e du [BDD](https://fr.wikipedia.org/wiki/Programmation_pilot%C3%A9e_par_le_comportement) et du [TDD](https://fr.wikipedia.org/wiki/Test_driven_development), je te laisse te renseigner sur l'Internet.

Symfony permet de faire simplement des tests unitaires et fonctionnels grâce à la librairie [phpUnit](https://symfony.com/doc/current/testing.html).

## Bonnes pratiques
Symfony définit les bonnes pratiques dans sa [documentation](https://symfony.com/doc/current/best_practices.html).

## Bref
Symfony permet encore de faire beaucoup. Voici une liste non exhaustive de ressources qui pourront t'aider à approfondir le sujet :
- [Symfony en 4h par Lior CHAMLA](https://www.youtube.com/playlist?list=PLpUhHhXoxrjdQLodxlHFY09_9XzqdPBW8)
- [Tester sur Symfony par Grafikart](https://www.youtube.com/playlist?list=PLjwdMgw5TTLWtWmdMzPaoc45Iztu7tVQ8)
- [Symfony 4 par l'exemple par Grafikart](https://www.youtube.com/playlist?list=PLjwdMgw5TTLX7wmorGgfrqI9TcA8nMb29)
- [Et plein d'autre ressources ! ](https://duckduckgo.com/?q=symfony)

# Bravo
Merci d'avoir suivi le cours jusqu'au bout et bravo, tu vas devenir un pro de Symfony !

![bravo](https://media.giphy.com/media/wijMRo7UZXSqA/giphy.gif)

> Code toujours comme si la personne qui allait maintenir ton code était un violent psychopathe qui sait où tu habites.
> - John Woods