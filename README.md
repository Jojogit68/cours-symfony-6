# Twig
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

[Twig](https://twig.symfony.com/) est un moteur de template PHP, à l'instar de Smarty, Blade, etc...

Le langage PHP qui était un moteur de gabarit à ses débuts est maintenant devenu un langage complet capable de supporter la programmation objet, fonctionnelle et impérative.

L'intérêt principal d'un moteur de gabarit est de séparer la logique de sa représentation. En utilisant PHP, comment définir ce qui est de la logique et ce qui est de la représentation ?

Pourtant, nous avons toujours besoin d'un peu de code dynamique pour intégrer des pages web :
- pouvoir boucler sur une liste d'éléments ;
- pouvoir afficher une portion de code selon une condition ;
- ou formater une date en fonction de la date locale utilisée par le visiteur du site...

Voici pourquoi Twig est plus adapté que le PHP en tant que moteur de gabarit :
- il a une syntaxe beaucoup plus concise et claire ;
- par défaut, il supporte de nombreuses fonctionnalités utiles, telles que la notion d'héritage ;
- et il sécurise automatiquement vos variables.

Comparaison d'un template PHP et Twig
``` html
<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to Symfony!</title>
    </head>
    <body>
        <h1><?php echo $page_title ?></h1>

        <ul id="navigation">
            <?php foreach ($navigation as $item): ?>
                <li>
                    <a href="<?php echo $item->getHref() ?>">
                        <?php echo $item->getCaption() ?>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </body>
</html>
```
``` twig
<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to Symfony!</title>
    </head>
    <body>
        <h1>{{ page_title }}</h1>

        <ul id="navigation">
            {% for item in navigation %}
                <li><a href="{{ item.href }}">{{ item.caption }}</a></li>
            {% endfor %}
        </ul>
    </body>
</html>
```

## Twig / VS Code / Emmet
Pour utiliser Emmet depuis des templates Twig dans VS Code, tu peux suivre [cette documentation](https://code.visualstudio.com/docs/editor/emmet). Pour changer les paramètres de VS Code, tu peux appuyer sur F1 (dans VS code hein) et taper ```Paramètres``` puis cliquer sur la proposition __Afficher les paramètres (en JSON)__. Un ```.json``` s'ouvre. Voici les paramètres à ajouter :

``` json
"emmet.syntaxProfiles": {
    "tpl": "html",
    "twig": "html"
},
"emmet.includeLanguages": {
    "smarty": "html",
    "twig": "html"
}
```

## Éléments de syntaxe

Twig supporte nativement trois types de syntaxe :
- ```{{ ... }}```  affiche une expression ;
- ```{% ... %}```  exécute une action ;
- ```{# ... #}```  jamais exécuté, utilisé pour des commentaires.

### Passage de variable
Dans __HomeController__ :
``` php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Bienvenue sur Duckzon',
        ]);
    }
}
```

Pour afficher une variable :  
```{{ ... }}``` : "Dit quelque chose" : imprime une variable ou le résultat d'une expression au template.

Tu peux tout effacer dans __home/index.html.twig__ et simplement écrire :
``` twig
<h1>{{ title }}</h1>
```
Vas voir sur [http://127.0.0.1:8000/](http://127.0.0.1:8000/), il devrait y être affiché Bienvenue sur Duckzon.

### Un peu d'explications
- Par défaut, Symfony ira chercher les templates dans le dossier ```templates```;
- la fonction render prend en paramètre le chemin vers le gabarit et un tableau de paramètres ;
- les paramètres sont disponibles dans le gabarit ;
- les extensions avant le ```.twig``` (```nom_du_fichier.html.twig```) permettent de savoir quel type de fichier est envoyé. En effet, pourquoi se contenter du format HTML ? Par exemple, si l'on a besoin de manipuler un fichier XML - disons un flux RSS -, nous pouvons tout à fait utiliser Twig pour cela, et nous nommerons le fichier flux.rss.twig, par exemple.

### Exercice
- Dans la méthode index, envoie une deuxième variable ```content``` au template, qui a la valeur ```Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem quam cum corrupti modi cupiditate nostrum odit illo veniam, nulla neque officia expedita rerum, aliquid libero incidunt rem iusto reprehenderit maxime!``` ;
- affiche cette variable dans le template.

---
---
---
![later](https://i.ytimg.com/vi/rh8m2-q71qQ/maxresdefault.jpg)
--- 
---
---

#### Correction
``` php
<?php
# src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Bienvenue sur Duckzon',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem quam cum corrupti modi cupiditate nostrum odit illo veniam, nulla neque officia expedita rerum, aliquid libero incidunt rem iusto reprehenderit maxime!'
        ]);
    }
}
```

``` twig
{# templates/home/index.html.twig #}
<h1>{{ title }}</h1>
<p>{{ content }}</p>
```

### Filtre et fonction

Twig possède un système de [filtre](https://twig.symfony.com/doc/2.x/filters/index.html), qui permet de changer la manière dont une variable sera affichée.

Une [fonction](https://twig.symfony.com/doc/2.x/functions/index.html) quant à elle, permet de changer la valeur d'une variable, ou encore d'appeler une fonction sans variable.

#### Exercice
- en te référant à la doc, essaye de mettre la variable ```title``` en majuscules.
- dans la méthode ```index``` du contrôleur, envoies une variable ```'date' => new \DateTime()``` et essaies de l'afficher au format ```d-m-Y```;
- dans le template, fait un _dump_ de la variable ```date```.
---
---
---
![later](https://i.ytimg.com/vi/rh8m2-q71qQ/maxresdefault.jpg)
--- 
---
---
##### Correction
- ```{{ title|upper }}```;
- ```{{ date|date("d-m-Y") }}```;
- ```{{ dump(date) }}```.

## Héritage
Quand tu crées la structure de ton site, tu es confronté·e à la problématique suivante :
> J'ai un design (sidebar, menu, etc...) mais je n'ai aucune envie d'avoir à recopier le code de mes éléments sur chaque page !

Pour pallier ce problème, tu peux faire hériter des template, d'autre templates.

### base.html.twig

Colle le code suivant dans ```base.html.twig```:
``` twig
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
    </head>
    <body>
        {% block body %}
            Hello Base !
        {% endblock %}
        {% block javascripts %}{% endblock %}
    </body>
</html>
```
Remplace le contenu de ```templates/home/index.html.twig``` par le code suivant :
``` twig
{% extends 'base.html.twig' %}
```
Et affiches la page [http://127.0.0.1:8000](http://127.0.0.1:8000).
Tu devrais voir affiché __Hello Base !__

Base va être le template de _base_ de l'application. Tous les autres templates vont _hériter_ de celui ci (pour rester simple).

> Tu peux aussi constater l'apparition de la DebugBar ! (tout en bas)

Maintenant, ajoute
``` twig
{% block body %}
    Hello Home !
{% endblock %}
```

Et affiche la page [http://127.0.0.1:8000](http://127.0.0.1:8000).
Tu devrais voir afficher __Hello Home !__

Maintenant, ajoute
``` twig
{% block body %}
    {{ parent() }}
    Hello Home !
{% endblock %}
```
Et affiche la page [http://127.0.0.1:8000](http://127.0.0.1:8000).
Tu devrais voir afficher __Hello Base ! Hello Home !__

L'héritage te permet de surcharger ou de redéfinir les blocs du template parent.

### Exercice
- Change la valeur du block title dans ```templates/home/index.html.twig``` en "Bienvenue sur DuckZon !";
- ajoutes la librairie [Bootstrap](https://getbootstrap.com/) au template ```templates/base.html.twig```.

---
---
---
![later](https://i.ytimg.com/vi/rh8m2-q71qQ/maxresdefault.jpg)
--- 
---
---

#### Correction
Dans ```templates/home/index.html.twig```:
``` twig
{% block title %}Bienvenue sur DuckZon !{% endblock %}
```

Dans le ```<head>``` de ```templates/base.html.twig```, ajoute ceci :
``` twig
{% block stylesheets %}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
{% endblock %}
```
Et juste avant la fermeture du ```<body>```:
``` twig
{% block javascripts %}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
{% endblock %}
```
## Autres éléments
### Ajouter du CSS et du JS
Si tu veux ajouter tes propres CSS et JS, tu peux rester simple et ajouter ces lignes :
``` html
<link href="{{ asset('css/main.css') }}" rel="stylesheet" />
<script src="{{ asset('js/main.js') }}"></script>
```
Et mettre les différents fichiers dans le dossier ```public```.

Pour compliquer, mais aussi dans l'éventualité où tu as besoin d'utiliser les dernières avancées en matière de dev front, tu peux générer tes assets avec Webpack (compilation, minification, interprétation, etc...): [https://symfony.com/doc/master/frontend.html](https://symfony.com/doc/master/frontend.html).  
On reparle plus tard.

### Inclusion
Tu as la possibité d'inclure des templates dans d'autres templates :
```{{ include ('_inc_/nac.html.twig') }}```
[L'inclusion](https://twig.symfony.com/doc/3.x/tags/include.html) est utile lorsque tu as des éléments qui ont une structure identique mais qui doivent intervenir de façon ponctuelle dans les pages et sont voués à être répétés.

Pour inclure une navigation, voici comment tu peux faire :
``` twig
{# templates/_inc/nav.html.twig #}
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Duckzon !</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Annonce</a>
                </li>
            </ul>
            <form class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Login</a></li>
                            <li><a class="dropdown-item" href="#">logout</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Register</a></li>
                        </ul>
                    </li>
                </ul>

                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>
```
``` twig
{# templates/base.html/twig #}
{# reste du fichier... #}
<body>
    {{ include ('_inc/nav.html.twig') }}
    {% block body %}{% endblock %}
</body>
```

### Executer une méthode de Controller
Dans un template, tu peux appeler une méthode de controller. Par exemple :
``` twig
{{ render(controller("App\\Controller\\AnnonceController:list", {'page':1})) }}
```
C'est utile si tu n'as pas les variables nécessaires à nos templates.

Pour en savoir plus : https://symfony.com/doc/current/reference/twig_reference.html#render

### Afficher un lien
``` twig
<a href="{{ path('nom_de_ma_route') }}">Nom de mon lien</a>
```
Pour voir le nom des routes, tu peux taper dans un terminal ```php bin/console debug:router```.

### Les boucles
``` twig
{% for article in articles %}
    <a href="{{ path('article_show', {'slug': article.slug}) }}">
        {{ article.title }}
    </a>
{% endfor %}
```

## Héritage sur 3 niveaux
Afin d'avoir un design flexible, tu peux créer des __layouts__, des _squelettes_ de pages. Tes layouts vont contenir la __structure__ de tes pages :
1. Une sidebar (ou non)
2. Un body
3. Un menu
4. Prendre toute la page ou non
5. etc...

Ajoutes un template __templates/_layout/full-width.html.twig__ avec ce code :
``` twig
{% extends "base.html.twig" %}

{% block body %}
    {{ include("_inc/nav.html.twig") }}
    <div class="container-fluid">
        {% block content %}{% endblock %}
    </div>
{% endblock %}

```

Et modifie __templates/home/index.html.twig__
``` twig
{% extends '_layout/full-width.html.twig' %}

{% block content %}
    <h1> Le contenu de ma home ! </h1>
{% endblock %}
```

### Exercice
- Crée un nouveau layout ```templates/_layout/sidebar.html.twig``` qui intégrera une sidebar à gauche avec un menu spécial en plus du menu de navigation ;
- fais hériter ```templates/annonce/index.html.twig``` de ce layout
- fais hériter le nouveau layout de ```templates/base.html.twig``` juste pour tester que cela fonctionne. On utilisera ce layout plus tard pour l'administration du site.

---
---
---
![later](https://i0.kym-cdn.com/entries/icons/original/000/010/437/Oneeternitylater.jpg)
--- 
---
---

#### Correction
``` twig
{# templates/_layout/sidebar.html.twig #}
{% extends "base.html.twig" %}

{% block body %}
{{ include("_include/nav.html.tiwg") }}
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2">
            {{ include("_inc/sidebar-nav.html.twig") }}
        </nav>
        <div class="col-md-10">
            {% block content %}{% endblock %}
        </div>
    </div>
</div>
{% endblock %}
```

``` twig
{# templates/_inc/sidebar-nav.html.twig #}
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="" class="nav-link">
            Lien 1
        </a>
    </li> 
</ul>
```

``` twig
{# templates/annonce/index.html.twig #}
{% extends '_layout/sidebar.html.twig' %}

{% block title %}Les annonces - DuckZon !{% endblock %}

{% block content %}

{% endblock %}

```


## Lien vers les pages
Pour créer un lien vers une page depuis twig, rien de plus simple !

Je te laisse chercher dans la [doc](https://twig.symfony.com/doc/3.x/) et mettre des liens là où tu peux. Vers /annonces par exemple, ou vers la home.

N'hésite pas à lister les routes grâce à la ligne de commande ```php bin/console debug:router```.

---
---
---
![later](https://i.ytimg.com/vi/sVoZBCwftb4/maxresdefault.jpg)
--- 
---
---

Par exemple dans __templates/_inc/nav.html.twig__ :
``` twig
<a href="{{ path('app_annonce_index') }}">Annonces</a>
```

### Lien actif dans le menu
Il faut renvoyer une variable depuis __AnnonceController__ :
``` php
public function index()
{
    return $this->render('annonce/index.html.twig', [
        'current_menu' => 'app_annonce_index'
    ]);
}
```
Et l'utiliser dans __templates/_inc/nav.html.twig__ :
``` twig
{# pour le lien vers annonce #}
<li class="nav-item">
    <a
        class="nav-link {% if current_menu is defined and current_menu == 'app_annonce_index' %} active{% endif %}"
        {% if current_menu is defined and current_menu == 'app_annonce_index' %} aria-current="page" {% endif %}
        href="{{ path('app_annonce_index') }}">Annonce
    </a>
</li>
```
