# Pré-requis

## Pré-requis théoriques
- Connaître les bases de la programmation en PHP ;
- avoir de bonnes notions de programmation orientée objet ;
- être à l'aise avec le design pattern [MVC](https://fr.wikipedia.org/wiki/Mod%C3%A8le-vue-contr%C3%B4leur) ;
- savoir cliquer sur les liens dans le cours, ils te permettront d'apprendre des concepts que tu ne connais (peut-être) pas.
## Pré-requis techniques

Avant d'installer Symfony, il convient de se rendre sur la [documentation](https://symfony.com/doc) du framework, dans la partie [Setup](https://symfony.com/doc/current/setup.html).

À l'heure où sont écrites ces lignes, Symfony en est à la version 5.0.

Pour connaître les pré-requis, rend-toi sur la page https://symfony.com/doc/current/setup.html#technical-requirements.

Voici donc ce dont tu auras besoin :
- PHP 7.2.5 ou plus récent, accessible en ligne de commande (CLI) avec les extensions suivantes Ctype, iconv, JSON, PCRE, Session, SimpleXML, and Tokenizer ;
- [Composer](https://getcomposer.org) qui est un [gestionnaire de dépendances](https://fr.wikipedia.org/wiki/Composer_(logiciel)), à la manière de ```apt``` pour _Debian_ et dérivés ([on parlera de gestionnaire de paquets](https://fr.wikipedia.org/wiki/Gestionnaire_de_paquets)) ou [```npm```](https://fr.wikipedia.org/wiki/Npm) pour _node.js_, ou encore [Bundler](https://bundler.io/) pour _Ruby_, mais, en l'occurence, pour PHP ;
- le [CLI Symfony](https://symfony.com/download), qui permettra de lancer certaines commandes ;
- un serveur MySql ;
- le logiciel [GIT](https://git-scm.com/) ;
- un terminal :
    - si tu es sur Windows tu peux utiliser CMD ou encore PowerShell, mais aussi :
        - le terminal git bash qui est installé avec [git pour Windows](https://git-scm.com/download/win) ;
        - [Windows Terminal](https://www.microsoft.com/fr-fr/p/windows-terminal-preview/9n0dx20hk701) ;
        - [cmder](https://cmder.net/) ;
        - le terminal de [VS Code](https://code.visualstudio.com/) en [mode git bash](https://code.visualstudio.com/docs/editor/integrated-terminal#_windows) (que je te conseille, en ayant installé git bash au préalable) ;
        - [etc](https://www.slant.co/topics/1552/~best-terminal-emulators-for-windows).
    - sous Linux: tu as l'embarras du choix :) mais voici quelques terminaux sympas :
        - [Tilix](https://gnunn1.github.io/tilix-web/) est un bon terminal ;
        - et aussi le terminal de [VS Code](https://code.visualstudio.com/).
- un éditeur de code ou un IDE
    - [VS Code](https://code.visualstudio.com/) (que je te conseille, car léger, et très bien) avec les extensions suivantes:
        - [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client) ;
        - [Twig Language 2](https://marketplace.visualstudio.com/items?itemName=mblode.twig-language-2) ;
        - [PHP Constructor](https://marketplace.visualstudio.com/items?itemName=MehediDracula.php-constructor) ;
        - [DotENV](https://marketplace.visualstudio.com/items?itemName=mikestead.dotenv) ;
        - [PHP Getters & Setters](https://marketplace.visualstudio.com/items?itemName=phproberto.vscode-php-getters-setters) ;
        - [YAML](https://marketplace.visualstudio.com/items?itemName=redhat.vscode-yaml) ;
        - [PHP Namespace Resolver](https://marketplace.visualstudio.com/items?itemName=MehediDracula.php-namespace-resolver) ;
        - tu peux installer toutes ces extensions via un terminal :
        ``` console
        code --install-extension bmewburn.vscode-intelephense-client && 
        code --install-extension mblode.twig-language-2 &&
        code --install-extension MehediDracula.php-constructor &&
        code --install-extension mikestead.dotenv && 
        code --install-extension phproberto.vscode-php-getters-setters &&
        code --install-extension redhat.vscode-yaml && 
        code --install-extension MehediDracula.php-namespace-resolver
        ```
    - [PhpStorm](https://www.jetbrains.com/fr-fr/phpstorm/) : super mais payant ;
    - [Apache NetBeans](https://netbeans.org/) : très bien aussi ;
    - [CodeLite IDE](https://codelite.org/) ;
    - etc...

## Linux (Debian et dérivés)
Tu peux utiliser une VM (avec [Vagrant](https://www.vagrantup.com/) par exemple), utiliser [Docker](https://www.docker.com/), ou encore installer les pré-requis directement sur ta machine.
### Installer PHP
Reporte-toi à [cette procédure](https://doc.ubuntu-fr.org/php).
### MySql
Reporte-toi à [cette procédure](https://doc.ubuntu-fr.org/mysql).
## Windows
### Installer Wamp
Rend-toi sur [http://www.wampserver.com](http://www.wampserver.com) et télécharge Wamp selon ton système.  
Clique sur le __.exe__ et suis les étapes d'installation.

### Vérifier que __PHP__ est bien dans le _path_ de Windows :
Pour se faire, ouvre le terminal de ton choix et tape ```php -v```  
Si la commande renvoie la version de __PHP__ et si la version est plus récente que la 7.2.5, c'est que tout est bon.
Sinon, il faudra [télécharger](http://windows.php.net/download) la dernière version de PHP et l'ajouter au _path_ de Windows.

#### Télécharger PHP
[Télécharge](http://windows.php.net/download) la version de PHP compatible avec ton système et déplace le dossier téléchargé dans _C:\wamp64\bin\php_ par exemple.

#### Ajouter PHP au path
1. Va dans les paramètres système avancés ```(Démarrer > Panneau de configuration > Système et sécurité > Système > Paramètres système avancés)``` ;
2. Clique sur le bouton ```Variables d'environnement...```  ;
3. Regarde dans le panneau ```Variables système```  ;
4. Trouves l'entrée ```Path```  (tu devrais avoir à faire descendre l'ascenseur pour le trouver) ;
5. Double-clique sur l'entrée ```Path``` ;
6. Entre ton répertoire __PHP__ à la fin, sans oublier le point-virgule ```;``` auparavant. C'est le répertoire dans lequel se trouve le fichier _php.exe_. Par exemple ```C:\wamp\bin\php\php7``` ;
7. Confirme en cliquant sur OK. Tu dois ensuite redémarrer l'invite de commandes pour prendre en compte les changements.
   Exécute à nouveau la commande ```php -v``` afin de voir si le problème est résolu.

## Tous systèmes
### Installer Symfony CLI
Suis la [documentation](https://symfony.com/download) selon ton système.
### Installer Composer
Suis la [documentation](https://getcomposer.org/download/) selon ton système.

## Well done
Tu peux tester les différentes installations dans un nouveau terminal grâce
aux commandes suivantes :
``` bash
symfony -V
# Symfony CLI version v4.14.1 (Wed Apr  8 10:40:30 UTC 2020)

composer -V
# Composer version 1.7.2 2018-08-16 16:57:12

php -v
# PHP 7.3.16-1+ubuntu18.04.1+deb.sury.org+1 (cli) (built: Mar 20 2020 13:51:46) ( NTS )
# Copyright (c) 1997-2018 The PHP Group
# Zend Engine v3.3.16, Copyright (c) 1998-2018 Zend Technologies
#     with Xdebug v2.9.2, Copyright (c) 2002-2020, by Derick Rethans
#     with Zend OPcache v7.3.16-1+ubuntu18.04.1+deb.sury.org+1, Copyright (c) 1999-2018, by Zend Technologies

```
Si tu n'as aucune erreur, tu es prêt·e pour la suite.

![well done](https://media.giphy.com/media/j44gNTTN0F4By/giphy.gif "well done")