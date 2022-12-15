# Liste des commandes
Toutes les commandes s'exécutent dans le dossier du projet et depuis un terminal
```shell
cd mon_projet/
```
## Symfony :
## Lancer le serveur de dev :
```shell
# serveur de base
php -S 127.0.0.1:8000 -t public
# serveur avancé
php bin/console server:run
# server Symfony
symfony serve
```
### Créer un Controller  
```shell
php bin/console make:controller DefaultController
```

### Liste de routes disponibles :
```shell
php bin/console debug:router
```

## Doctrine : 
[https://symfony.com/doc/current/doctrine.html]()
### Générer sa base de donnée
```shell
php bin/console doctrine:database:create
```
### Générer une entité
```shell
php bin/console make:entity EntityName
```
### Mettre à jour les tables
```shell
# Pour voir la requête qui va être executé
php bin/console doctrine:schema:update --dump-sql
# Pour lancer la mise à jour des table
php bin/console doctrine:schema:update --force
```
### Générer les getter et les setter
```shell
# N'existe plus dans Symfony 4
php bin/console doctrine:generate:entities /Namespace/To/Entity
```
Cette commande n'existe plus dans Symfony 4, néanmois, certains IDE proposent des plugins :  
- Pour __VSCode__ : phproberto.vscode-php-getters-setters

### Faire une requête SQL
```shell
php bin/console doctrine:query:sql 'SELECT * FROM product'
```

### Créer un système utilisateur simple
```shell
php bin/console make:user 
php bin/console make:auth
php bin/console make:registration-form
``` 

### Créer un système CRUD
```shell
php bin/console make:crud 
``` 
