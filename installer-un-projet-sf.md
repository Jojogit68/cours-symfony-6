# Installer un projet Symfony

## Prérequis
- Avoir la version de php adequate, au moins en CLI (ligne de commande) -> voir les variables d'environnements de Windows
- Avoir la version de php adequate pour apache -> voir wamp sur windows
- Installer Composer https://getcomposer.org/download/
- Installer Symfony https://symfony.com/download
- Installer GIT

## Pour installer un projet
Dans un terminal :
```shell
# clone du projet depuis git
git clone https://url-du-projet
# installation des dépendances
cd nom_du_projet
composer install
## création du fichier de configuration
touch .env.local
# ajout des informations de connexion à la DB dans le .env.local
echo DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name >> .env.local
# création de la base de données
php bin/console doctrine:database:create
# création du schema de la base de données (utile en prod)
php bin/console doctrine:migration:migrate
# ou en dev
php bin/console doctrine:schema:update --force
# création de jeu de données en DB
php bin/console doctrine:fixtures:load
# lancement du serveur de dev
symfony server:start
```
Votre projet est installé !