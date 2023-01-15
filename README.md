# Écrire du CSS et du JavaScript moderne

> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

Pour écrire du CSS et du JavaScript dans une application Symfony, nous avons plusieurs méthodes.  
La première consiste à placer les fichiers CSS et JS dans le dossier __public__, puis d'appeler ces fichiers 
comme nous le ferions dans une application classique avec la fonction __twig__ ```asset``` en plus :

```twig
{% block stylesheets %}
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
{% endblock %}

{% block javascripts %}
    <script src="{{ asset('js/app.css') }}"></script>
{% endblock %}
```

Tu peux en apprendre plus sur cette fonction en lisant la [documentation](https://symfony.com/doc/current/reference/twig_reference.html#asset).

La deuxième consiste à utiliser un __module bundler__ ! Je te laisse te renseigner sur le sujet avant d'attaquer la suite. 

> Voici quelques ressources pour comprendre ce qu'est un __module bundler__ : 
> 
> - [video de Grafikart](https://grafikart.fr/tutoriels/module-bundler-979)
> - [article sur lihautan.com en anglais](https://lihautan.com/what-is-module-bundler-and-how-does-it-work/)
> - [article sur snipcart.com en anglais](https://snipcart.com/blog/javascript-module-bundler)

Il existe plusieurs modules bundler populaires. Symfony a fait le choix d'intégrer [Webpack](https://webpack.js.org/).

Pour installer Webpack dans une application Symfony, nous pouvons utiliser un outil développé par Symfony qui se nomme [Webpack Encore](https://symfony.com/doc/current/frontend.html#webpack-encore).

En prérequis, il faut installer 

- [NodeJS](https://nodejs.dev/en/) sur ta machine. Attention ! Sur Linux il est préférable d'utiliser [Nvm](https://github.com/nvm-sh/nvm) pour installer la dernière version de NodeJS plus facilement. 
  Il n'est pas forcément nécessaire d'installer Yarn comme [indiqué dans la documentation](https://symfony.com/doc/current/frontend/encore/installation.html).
- et [Npm](https://www.npmjs.com/) qui est normalement installé par défaut avec NodeJS. Npm est un gestionnaire de paquet, comme Composer, mais pour JavaScript.

Pour vérifier que NodeJS et Npm sont bien installés, tu peux lancer ces commandes dans un nouveau terminal :

```shell
node -v
npm -v
```

Tu es prêt à installer __Webpack Encore__ !

## Installation

Tu trouveras toutes les étapes d'installation dans la [documentation officielle](https://symfony.com/doc/current/frontend/encore/installation.html).

Depuis un terminal, rends-toi à la racine du projet Symfony et lance les commandes suivantes :

```shell
composer require symfony/webpack-encore-bundle
# puis
npm install
```

Et voilà :) __Webpack Encore__ est installé !

Souviens-toi simplement que dorénavant, pour installer une application Symfony déjà commencée,
il faudra non seulement lancer ```composer install``` pour installer les dépendances PHP, mais aussi ```npm install```
pour installer les dépendances JavaScript.

## Configuration

Un nouveau fichier __webpack.config.js__ a été créé à la racine du projet. Je te laisse l'ouvrir et regarder son contenue.  
Voici pour la configuration de base :

```javascript
Encore // un objet JavaScript Encore
    // le répertoire où va aller les fichiers compilés
    .setOutputPath('public/build/')
    // le chemin public utilisé par le serveur web pour accéder aux fichiers
    .setPublicPath('/build')

    /**
     * ENTRY CONFIG
     *
     * Ici ce sont les "entrées"
     * Les entrées sont des fichiers JacaScript dans lesquels on va importer les autres fichiers 
     * Javascript, les modules Javascript, les CSS ou SCSS par exemple, les images, etc...
     * Le fichier d'entrée par défaut est situé dans /assets/app.js. 
     * C'est dans ce fichier que l'on peut importer le CSS de Bootstrap par exemple.
     * 
     * Pour chaque entrée, un fichier JacaScript compilé sera créé (ex: /public/build/app.js)
     * si le fichier JavaScript import du CSS, un fichier CSS compilé sera créé (ex: /public/build/app.css)
     */
    .addEntry('app', './assets/app.js')
```

## Écrire et compiler du JavaScript et du CSS

### Où écrire

Tous les fichiers JavaScript et CSS seront dorénavant écrit dans le dossier __/assets__.

Je te propose d'ouvrir le fichier d'entrée __/assets/app.js__ et d'y écrire un petit bout de code JavaScript :

```javascript
/**
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// c'est comme cela qu'on importe un fichier CSS
import './styles/app.css';

// start the Stimulus application
// permet de charger la biblihotèque Stimulus https://stimulus.hotwired.dev/ 
// et pas Bootstrap comme on pourrait le penser !
import './bootstrap';

// le bout de code à ajouter
alert('Webpack fonctionne !')
```

Tu peux aussi écrire ce petit bout de CSS dans le fichier __/assets/styles/app.css__ :

```css
body {
    background-color: red;
}
```

### Comment compiler

Lance la commande suivante, qui va permettre de compiler les fichiers dans le dossier __/public/build__ :

```shell
npm run build
```

Et vas voir dans le dossier __/public/build__ : les fichiers __app.4399ab99.js__ et __app.b58abbbb.css__ ont été compilé ! Tu peux les ouvrir pour voir leur contenu.

> Mais c'est quoi ces noms de fichier ?!

### Une histoire de cache (cache)

Tu n'es pas sans savoir que les navigateurs mettent en caches certaines ressources telles que les images, les fichiers CSS, JavaScript, etc... 
Si le navigateur trouve le nom du fichier dans son cache, par exemple __app.css__, il va charger le fichier mis en cache.  
Cela évite une requête vers le serveur et permet que le site se charge plus vite. 

C'est pour cela que parfois, quand tu changes le CSS d'un site, les changements ne se sont pas visibles tout de suite. 
C'est certainement qu'il faut vider le cache !

> Le cache est un instrument du navigateur qui lui permet de sauvegarder les données (images, langage HTML...) nécessaires 
> à la consultation d'un site web, sous forme de fichiers temporaires. 
> L'intérêt de ces sauvegardes est de réduire la consommation de bande passante. 
> [source](https://blog.hubspot.fr/website/qu-est-ce-que-le-cache-dun-navigateur) 

![cache cache](https://media.giphy.com/media/4TgADFrAKzq2wKyomS/giphy-downsized-large.gif)

Les numéros entre __app__ et __.css/.js__ sont des numéros aléatoires, qui permettent qu'à chaque nouvelle compilation, 
le navigateur recharge le fichier sans tenir compte du cache navigateur. 
Si tu relances la commande ```npm run build```, les numéros auront changé.

Comme nous l'avons rappelé, dans une application classique, pour lier du CSS ou du JavaScript, il faut écrire :

```twig
{% block stylesheets %}
    <link rel="stylesheet" href="css/styles.css">
{% endblock %}

{% block javascripts %}
    <script src="js/app.css"></script>
{% endblock %}
```

Mais comment faire pour lier les fichiers compiler qui changent à chaque fois de nom ? 

Symfony a la solution évidemment ! Ouvre le fichier __/templates/base.html.twig__ et ajoute ces lignes dans les block correspondants :

```twig
{% block stylesheets %}
    {{ encore_entry_link_tags('app') }}
{% endblock %}

{% block javascripts %}
    {{ encore_entry_script_tags('app') }}
{% endblock %}
```

```encore_entry_link_tags('app')``` permet de charger le fichier CSS qui correspond à l'entrée ```app``` définit dans le fichier de configuration.  
Idem pour ```encore_entry_script_tags('app')``` mais pour le JavaScript.

Voilà, en rechargeant ton application depuis le navigateur, tu devrais voir apparaître une alerte et un beau fond rouge !
![Rouge !](https://media.giphy.com/media/3otPoT7JlwohDEipKE/giphy.gif)

> N'oublie pas que tout ce que nous avons vu est dans la [documentation](https://symfony.com/doc/current/frontend/encore/installation.html)

### Compiler autrement

`npm run build` permet de compiler pour la production. Les fichiers sont minifiés, optimisés, et presque impossible à lire. Ce n'est pas pratique lorsqu'on développe...

Nous pouvons donc utiliser la commande `npm run dev` pour une compilation plus sympathique pour les développeurs. 

Et encore mieux, nous pouvons utiliser `npm run watch` qui va compiler dès qu'un fichier est sauvegarder ! Pratique si tu travaille beaucoup sur le CSS ou le JS !

Toutes ces commandes sont listé dans le fichier __packages.json__ :

```json
    "scripts": {
        "dev-server": "encore dev-server",
        "dev": "encore dev",
        "watch": "encore dev --watch",
        "build": "encore production --progress"
    }
```

## Ajouter Bootstrap

### Ajouter le style

Nous avions déjà ajouté Bootstrap grâce au lien CDN, mais nous pouvons aussi l'ajouter avec NPM et ainsi pouvoir personnaliser le thème plus facilement.  
Par exemple, changer la couleur primaire en orange, afin que tous les boutons avec la classe ```btn btn-primary``` soient orange au lieu de bleu. 

En premier lieu, rends-toi sur la documentation [Using Bootstrap CSS & JS (Symfony Docs)](https://symfony.com/doc/current/frontend/encore/bootstrap.html).  
Comme indiqué, il suffit de lancer la commande ```npm install bootstrap --save-dev```. 
Une fois l'installation terminée, tu peux aller voir dans le dossier __/node_modules/bootstrap__, afin de voir comment Bootstrap est écrit.  
Comme tu peux le voir, les fichiers possèdent l'extension ```.scss```. C'est du CSS amélioré ! Je te laisse regarder la documentation de [__SASS__](https://sass-lang.com/) et voir à quoi il sert sur [Sass (langage) — Wikipédia](https://fr.wikipedia.org/wiki/Sass_(langage)).

Ensuite il faut créer une fichier __/assets/styles/global.scss__ avec le contenue suivant :

```scss
// personnalistaion des variables Bootstrap
$primary: #A6294B;
$secondary: #D9AE5F;

// import de la librairie Bootstrap. Le ~ reference le dossier node_modules
@import "~bootstrap/scss/bootstrap";
```

Vu que Bootstrap est écrit en `.scss` et que nous avons nous même écrit un fichier `.scss` il faut spécifier à **Webpack Encore** comment compiler ce type de fichier. Rends toi dans __webpack.config.js__ et dé-commente la ligne `.enableSassLoader()`

### Ajouter le JavaScript

Pour ajouter le JavaScript, il faut lancer la commande suivante, comme indiqué dans la documentation `npm install jquery @popperjs/core --save-dev`. Puis dans le fichier  **/assets/app.js** 

```javascript
const $ = require('jquery');
require('bootstrap');
```

### Supprimer les liens CDN

Maintenant que nous avons ajouté les librairie Bootstrap et jQuery via NPM, nous n'avons plus besoin des liens CDN, tu peux donc les supprimer.

Voici à quoi devrait ressembler les blocks **stylesheets** et **javascripts** dans le fichier __/templates/base.html.twig__

```twig
{% block stylesheets %}
    {{ encore_entry_link_tags('app') }}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{% endblock %}
```

```twig
{% block javascripts %}  
    {{ encore_entry_script_tags('app') }}  
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>  
    <script>$('select').select2()</script>  
{% endblock %}
```

Compile à nouveau avec `npm run build` et recharge la page de l'application. Le style devrait encore fonctionner, ainsi que le JavaScript !

## Select2

Et oui, pourquoi s'arrêter en si bon chemin, nous pouvons aussi installer et utiliser la librairie Select2 via NPM ! Voici le [lien vers la librairie](https://www.npmjs.com/package/select2).

Je te laisse essayer tout seul de faire fonctionner Select2 sans les CDN. C'est à dire que les blocks **stylesheets** et **javascripts** dans le fichier **/templates/base.html.twig** devraient ressembler à ceci :

```twig
{% block stylesheets %}
    {{ encore_entry_link_tags('app') }}
{% endblock %}
```

```twig
{% block javascripts %}  
    {{ encore_entry_script_tags('app') }}   
{% endblock %}
```

![](https://static.wikia.nocookie.net/spongebob/images/6/6a/I_Was_a_Teenage_Gary_063.png/revision/latest?cb=20211011001338)

### Correction

Voici comment tu peux t'en sortir. En premier lieu il faut installer la librairie : ``npm i select2 --save-dev`

Ensuite il faut importer le JavaScript de Select2. Je te propose de l'importer dans __/assets/app.js__ puisque nous avons besoin de la librairie un peu partout sur le site :

```javascript
// reste du code...
import 'select2';
$(document).ready(function() { // permet de lancer Select2 sur tous les élements avec la classe 'select2'
    $('select').select2()
});
```

Enfin il faut importer le CSS de la librairie. Tu peux le faire dans __/assets/styles/global.scss__ :

```scss
// reste du code...
@import "~select2/dist/css/select2.css";
```

Il ne reste plus qu'à compiler, et voilà !

![](https://media.giphy.com/media/8P1oO2JbrZK2uSYnL6/giphy.gif)