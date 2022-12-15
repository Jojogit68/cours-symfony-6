# Déployer une application Symfony
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).  

Ton site fonctionne en local, super !
Maintenant, il serait souhaitable que ton travail profite à un maximum de personnes ! 
Nous allons voir ici comment déployer une application Symfony sur un serveur distant.

## Préparer son application
Avant de déployer ton application, il va être utile de tester si celle-ci fonctionne bien en local.

### Vider le cache
Pour être sûr qu'il n'y ait pas de code résiduel, tu peux vider le cache de l'environnement de dev en faisant : 
```shell 
php bin/console cache:clear
```  
Cette commande va vider le contenu de ```var/cache/dev```. 
Si tu regardes dans ce dossier, tu verras que Symfony a compilé beaucoup de choses comme les templates twig, les entités, les traductions, etc...  
Cette mise en cache permet à Symfony d'aller plus vite lors de son exécution. 
Il peut arriver que du code ancien (pas trop ancien attention), qui fonctionne, soit encore en cache, alors que notre code ne fonctionne peut-être plus.
D'où l'importance de vider le cache.  
Il convient aussi de supprimer le cache de l'environnement de production en faisant
```shell
# supprime var/cache/prod
php bin/console cache:clear --env=prod 
# recréer des caches nécessaires (entités par exemple)
php bin/console cache:warmup --env=prod 
```

### Parcourir en mode prod
Tu peux parcourir ton application comme si tu étais un utilisateur qui visite ton site en production. 
Pour cela, rendez-vous dans le fichier ```.env.local``` et changes la valeur ```APP_ENV``` à ```"prod"``` et de ```APP_DEBUG``` à ```0``` : 
```apacheconf
# .env.local
APP_ENV=prod
APP_DEBUG=0
# ...
```
> Quand le mode de débogage est désactivé, les erreurs n'apparaissent pas à l'écran. 
> Cependant, elles sont répertoriées dans le fichier  var/log/prod. 
> Si l'un de tes utilisateurs te remonte une erreur, il faudra regarder dans ce fichier pour connaître le détail de l'erreur et les informations nécessaires à sa résolution.

### Pages d'erreur
Les pages d'erreurs ne sont, par défaut, pas très sympathique en environnement de production. 
Pour t'en rendre compte, crées une erreur sur une page (une erreur de syntaxe par exemple). 

Mais il est possible de personnaliser ces pages d'erreur ! Il suffit de lire la [documentation](https://symfony.com/doc/current/controller/error_pages.html).

### Vérifiez la sécurité des dépendances

Il y a beaucoup de dépendances dans un projet Symfony. 
Il est donc pratiquement impossible d'être au courant de toutes les failles de sécurité découvertes dans ces dépendances.
Et pourtant, c'est indispensable. 
En effet, tu ne veux pas mettre en ligne une application alors que certaines des dépendances contiennent des failles de sécurité n'est-ce pas ?

Pour gérer cela, un outil a été créé par SensioLabs : Security Checker.  
Tu peux lancer cette ligne de commande pour tester.
```shell
symfony check:security

Symfony Security Check Report
=============================

No packages have known vulnerabilities.


Note that this checker can only detect vulnerabilities that are referenced in the security advisories database.
Execute this command regularly to check the newly discovered vulnerabilities.
```
Si ton projet contient une dépendance avec une faille déjà répertoriée, renseigne-toi sur internet. 
La plupart du temps, la bibliothèque aura corrigé la faille dans une version plus récente : tu n'auras qu'à mettre la dépendance à jour.

## Déploiement sur un serveur LAMP
![serveur lamp](https://media.giphy.com/media/qkk7SDP5XVxsI/giphy.gif)  
Nous allons voir la façon la plus "classique" de déployer une application Symfony sur un serveur [LAMP](https://fr.wikipedia.org/wiki/LAMP) (Linux Apache Mysql Php).

L'idéal est d'avoir un accès en SSH et d'utiliser un gestionnaire de code source tel que GIT. 
Ainsi, il suffira de faire un ```git pull``` sur le serveur pour mettre à jour l'application. 

Si tu n'as pas d'accès SSH, tu peux tout à fait utiliser un accès FTP et transférer tous les fichiers sur le serveur.

Quoi qu'il en soit, il y a plusieurs points à respecter pour installer une application Symfony en local ou sur un serveur :
- la machine doit avoir les pré-requis décrits dans la [documentation](https://symfony.com/doc/current/setup.html#symfony-tech-requirements) : la version de PHP et les extensions ;
- une application Symfony a besoin des bibliothèques contenues dans ```vendor```, à installer avec Composer ou à uploader sur le FTP ;
- il faut configurer les variables d'environnements : les identifiants de connexion à la base de données, l'environnement (prod, dev, test...), et tout autres variables nécessaires. En production, [plusieurs méthodes sont possibles](https://symfony.com/doc/current/deployment.html#b-configure-your-environment-variables) ;
- il faut créer une base de données et lancer la mise à jour de celle-ci.

Nous allons partir du principe que tu as un serveur LAMP déjà configuré avec un accès SSH et une url qui pointe vers ce serveur.
Si tu souhaites installer ton propre serveur, voici un [tuto assez complet](https://www.youtube.com/playlist?list=PLjwdMgw5TTLUnvhOKLcpCG8ORQsfE7uB4).
Autrement tu peux toujours louer un serveur pré-configuré chez l'hébergeur de ton choix, en vérifiant bien les pré-requis.

Avant de passer à l'étape suivante, il faut installer le packages [```apache-pack```](https://symfony.com/doc/current/setup/web_server_configuration.html) qui permet de configurer une application Symfony pour Apache :
```shell
composer require symfony/apache-pack
```
> N'oublie pas, avant de passer à la suite, de commit et push si tu versionnes ton projet 
### SSH et GIT
La façon la plus simple (à mon sens) pour déployer une application (n'importe laquelle), est d'utiliser le combo SSH et GIT.
En effet
- GIT est plus rapide que FTP en ce qui concerne le transfert de fichier ;
- GIT permet d'avoir une application à jour avec le dépôt ;
- il est facile de revenir en arrière si un bug est introduit ;
- etc...

> Attention, les images, ou encore les fichiers téléversés par exemple, ne devraient pas être versionnées, en effet cela peut devenir lourd en terme d'espace disque.
> Il faudra donc les stockés sur le serveur ou encore avec un service dédié (sur le cloud par exemple).

La première chose à faire est de [se connecter au serveur en SSH](https://doc.ubuntu-fr.org/ssh) et d'installer les logiciels nécessaires au déploiement s'ils ne sont pas installés.

```shell
ssh user@ip.ou.url.du.serveur
```
Tu peux aussi te connecter en SSH avec [PuTTY](https://putty.org/) sur Windows.

#### Pré-requis
Une fois connecté, et pour savoir si les logiciels nécessaires sont installés, tu peux lancer les lignes de commandes suivantes :
```shell
git --version
php -v
composer --version
mysql --version
apache2 -v
symfony -V # non obligatoire
```

Si tous ces utilitaires sont installés, tu peux passer à la suite.

#### Clone du projet
Tu peux te déplacer vers le dossier accessible depuis le port 80 (le port écouté par Apache par défaut), qui en général ```/var/www/html```, mais cela peut être différent selon les serveurs !  
Il se peut que le dossier soit ```/home/ton_user/ton_site/public_html```. Cela dépend de la configuration Apache.
```shell
cd /home/ton_user/ton_site
```

Puis clone ton projet dans le dossier accessible :
```shell
git clone https://url-de-ton-projet public_html
```
> Le deuxième paramètre permet de cloner un dépôt dans un dossier, ici ```public_html```.    

Il se peut que GIT renvoie une erreur car le dossier n'est pas vide. 
Il suffit de supprimer son contenu avec ```rm -rf public_html/*``` (s'il n'y a rien d'important !) et de relancer la commande ```git clone```.

#### Installation des vendor
Une fois que le projet est cloné, il faut installer les vendor :
```shell
cd public_html
composer install
```
Les bibliothèques vont s'installer.

#### Configuration des variables d'environnements
Toujours dans le dossier ```public_html```, crée un fichier ```.env.local``` avec le contenu suivant (à adapté à ton cas) :
```shell
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
```
##### Perfomance du .env
```shell
# créer un fichier .env.local.php, plus rapide à lire que le .env.local
composer dump-env prod
```

#### Création ou mise à jour de la base de données
Toujours dans le dossier ```public_html```, et si la base de données n'est pas créée, lance la commande :
```shell
php bin/console d:d:c
```
Il faut ensuite mettre à jour le schema avec :
```shell
php bin/console d:m:m 
# ou si aucun fichier de migration n'existe
php bin/console d:s:u --force
```

#### Vhost ou .htaccess
Le _front controller_ qui permet de lancer l'application Symfony se trouve dans le dossier ```public``` de l'application. 
Si tu regardes dans ce dossier, tu trouveras un fichier ```index.php``` que tu peux ouvrir. 
C'est ce fichier qui doit être exécuté lorsque l'on va taper l'url de ton site.

Tu peux donc configurer ton Vhost apache si tu en as la possibilité comme indiqué dans la [documentation](https://symfony.com/doc/current/setup/web_server_configuration.html).

Si ce n'est pas possible, tu peux ajouter un fichier ```.htaccess``` à la racine du projet avec le contenu suivant :
```apacheconf
RewriteEngine On
RewriteBase /public

RewriteCond %{THE_REQUEST} /public/([^\s?]*) [NC]
RewriteRule ^ %1 [L,NE,R=302]
RewriteRule ^(.*)$ public/index.php?$1 [L,QSA]
```

#### Test ton application
Ton application devrait être fonctionnelle en tapant l'url de ton site !

#### Mise à jour
Pour mettre à jour une application déployer avec GIT voici les étapes à suivre (dans l'ordre) :
- mettre à jour le code : un simple ```git pull``` devrait faire l'affaire ;
- mettre à jour les bibliothèques : ```composer install``` ;
- mettre à jour la base de données :
    - si ton application est déjà en service et que tu as des données, il est bon de faire des fichiers de migration dans ton environnement de dev et de lancer ces migrations en prod avec ```php bin/console d:m:m``` ;
    - si ton application n'est pas encore en service et que les données ne sont pas (encore) importantes, tu peux lancer ```php bin/console d:s:u --force```.
- etc...

![deploy OK](https://media.giphy.com/media/3og0IAQG2BtR13joe4/giphy.gif)

## Autres manières de déployer
- [Documentation officielle](https://symfony.com/doc/current/deployment.html)
- [Tutoriel Symfony : Héberger le site sur un hébergement mutualisé](https://www.youtube.com/watch?v=AAap9qRHgIk)
- [Formation Symfony 4 - Épisode 13 - Déploiement avec Heroku](https://www.youtube.com/watch?v=XEdFtzq0RYo)
- [SymfonyLive Paris 2017 - Déployer une app Symfony dans un PaaS](https://www.youtube.com/watch?v=DhhooojScM8)
