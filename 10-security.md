# Le composant Security
> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).  

Dans une application, on a souvent un système utilisateur, afin qu'un visiteur 
puisse s'inscrire, se connecter et faire des actions selon ses rôles. 
Dans notre application, nous pouvons déjà créer deux types d'utilisateurs :
- les simples utilisateurs pourront :
    - ajouter, modifier et supprimer leurs annonces ;
    - éditer leur profil ;
- les administrateurs pourront :
    - gérer toutes les annonces ;
    - gérer les utilisateurs.

Pour cela, Symfony met à notre disposition le composant [Security](https://symfony.com/doc/current/security.html).

Nous allons créer un système utilisateur en 2 temps 3 mouvements. Mais avant, prends 10 petites minutes pour survoler la [documentation](https://symfony.com/doc/current/security.html).

## Création de l'entité User
Il va te falloir une entité User qui va représenter un utilisateur. Pour cela, Symfony dispose de la commande suivante :

```shell
php bin/console make:user # et non pas make:entity
```
Il convient de répondre comme ci-dessous (les chaîne de caractère entre crochet ```[User]``` sont les réponses par défauts) :
```shell
The name of the security user class (e.g. User) [User]:
 > 

 Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
 > 

 Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
 > 

 Will this app need to hash/check user passwords? Choose No if passwords are not needed or will be checked/hashed by some other system (e.g. a single sign-on server).

 Does this app need to hash/check user passwords? (yes/no) [yes]:
 > 

 created: src/Entity/User.php
 created: src/Repository/UserRepository.php
 updated: src/Entity/User.php
 updated: config/packages/security.yaml

           
  Success! 
           

 Next Steps:
   - Review your new App\Entity\User class.
   - Use make:entity to add more fields to your User entity and then run make:migration.
   - Create a way to authenticate! See https://symfony.com/doc/current/security.html
```
Symfony a créé / mis à jour plusieurs fichiers. Notamment le fichier __config/packages/security.yaml__ 
qui contient toute la configuration concernant le composant et le fichier __src/Entity/User.php__ (n'hésite pas à ouvrir ces fichiers).

### Exercice
Ajoute les champs suivants à l'entité _User_ :
- lastname / string / 255 / nullable:yes;
- firstname / string / 255 / nullable:yes;
- nickname / string / 255 / nullable:false;
- mets à jour la base de données.

---
---
---
![Later](https://i.ytimg.com/vi/6kpxjIbbDT0/maxresdefault.jpg)  

---
---
---

#### Correction
Ajout des propriétés :
```shelll
php bin/console make:entity User
```
Mise à jour de la base de données :
```shell
php bin/console make:migration
php bin/console doctrine:migration:migrate
```

## Création d'un register-form
> Ok, nous avons une entité et une table User. Mais à quoi cela sert si les utilisateurs ne peuvent pas s'inscrire ??? 

Très bonne question !

Avec Symfony, il est possible de créer un formulaire de création en __UNE SEULE__ ligne de commande

Je te propose de lancer la commande ```php bin/console list make``` et de trouver une commande en rapport avec _register_... 

Une fois la commande trouvée, lance là et laisse-toi guider :)
(dis __non__ si Symfony te demande d'envoyer un mail pour confirmer l'inscription, cela complique les choses, et on souhaite rester simple)

---
---
---
![Later](https://media.giphy.com/media/kpzfYwBT7nUVW/giphy.gif)

---
---
---

Sans surprise, je te propose de lancer la ligne de commande suivante :
```shell
php bin/console make:registration-form

Creating a registration form for App\Entity\User

 Do you want to add a @UniqueEntity validation annotation on your User class to make sure duplicate accounts aren\'t created? (yes/no) [yes]:
 > 

 Do you want to send an email to verify the user's email address after registration? (yes/no) [yes]:
 > no

 Do you want to automatically authenticate the user after registration? (yes/no) [yes]:
 > 

 updated: src/Entity/User.php
 created: src/Form/RegistrationFormType.php
 created: src/Controller/RegistrationController.php
 created: templates/registration/register.html.twig

           
  Success! 
           

 Next: Go to /register to check out your new form!
 Make any changes you need to the form, controller & template.
```
Symfony vient de créer / mettre à jour plusieurs fichiers et t'indique que tu peux aller sur __/register__ pour voir ton formulaire.

Voici à quoi devrait ressembler __src/Controller/RegistrationController.php__:
```php
<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        LoginFormAuthenticator $loginFormAuthenticator,
        UserAuthenticatorInterface $authenticator
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $authenticator->authenticateUser($user, $loginFormAuthenticator, $request); // permet d'authentifier l'utilisateur
            return $this->redirectToRoute('app_home_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

```

Tu peux d'ailleurs ajouter le champ __nickname__ au formulaire :
```php
# src/Form/RegistrationFormType.php
//...
public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('nickname')
            //...
```

```php
{# templates/registration/register.html.twig #}
{# ... #}
{{ form_start(registrationForm) }}
    {{ form_row(registrationForm.email) }}
    {{ form_row(registrationForm.nickname) }}
    {# ... #}
```
N'hésites pas à aller sur la route __/register__ et de t'inscrire sur ton __FA-BU-LEUX__ site.

## Création du système de d'AUTHentification
Nous allons créer un système d'**AUTH**entification très rapidement, avec :
- un formulaire de login déjà fait ;
- le système de recherche de l'utilisateur en base de données lors de la soumission du formulaire ;
- et le stockage de l'utilisateur en session.

Et tout cela en __UNE__ ligne de commande. 
Je te laisse essayer de trouver __cette__ ligne de commande en tapant ```php bin/console list make```.

---
---
---
![Later](https://media.giphy.com/media/tXL4FHPSnVJ0A/giphy.gif)  

---
---
---

Tu l'auras deviné, la ligne de commande en question est la suivante : ```php bin/console make:auth``` 
(si tu n'avais pas trouvé, remarques le ```auth``` les _indices_ subtils que j'ai laissé plus haut).


Je te propose de lancer sans plus attendre cette commande et de répondre comme suit :
```shell
php bin/console make:auth

 What style of authentication do you want? [Empty authenticator]:
  [0] Empty authenticator
  [1] Login form authenticator
 > 1

 The class name of the authenticator to create (e.g. AppCustomAuthenticator):
 > LoginForm

 Choose a name for the controller class (e.g. SecurityController) [SecurityController]:
 >  

 Do you want to generate a '/logout' URL? (yes/no) [yes]:
 > 

 created: src/Security/LoginFormAuthenticator.php
 updated: config/packages/security.yaml
 created: src/Controller/SecurityController.php
 created: templates/security/login.html.twig

           
  Success! 
           

 Next:
 - Customize your new authenticator.
 - Finish the redirect "TODO" in the App\Security\LoginFormAuthenticator::onAuthenticationSuccess() method.
 - Review & adapt the login template: templates/security/login.html.twig.
```

Symfony vient de créer / mettre à jour plusieurs fichiers et t'indique qu'il faut faire plusieurs choses :
```shell
- Finish the redirect "TODO" in the App\Security\LoginFormAuthenticator::onAuthenticationSuccess() method.
- Review & adapt the login template: templates/security/login.html.twig.
```

Je te propose donc de faire ce qu'il t'indique dans les différents fichiers.

Dans le fichier __src/Security/LoginFormAuthenticator.php__, dans la méthode ```onAuthenticationSuccess``` (qui se déclenche... suspense... si l'utilisateur a réussi à s'authentifier (il a rentré ses bons identifiants)), tu peux fournir une route vers laquelle l'utilisateur sera redirigé : ```// For example : return new RedirectResponse($this->urlGenerator->generate('le nom de ta route'));```.


Tu peux aller sur la route __/login__ et tu devrais voir un formulaire de login. Que tu peux modifier comme indiqué par Symfony ```Review & adapt the login template: templates/security/login.html.twig.```.

Symfony a aussi créé une route __/logout__ qui permet de déconnecter un utilisateur !

## Fixtures
Tu as, pour le moment, peu d'utilisateurs en base de données. Mais maintenant, tu sais que tu peux remplir la base de données avec 15000 utilisateurs grâce aux __fixtures__ !

### Exercice
Je te propose d'essayer de créer une nouvelle fixture pour ajouter quelques utilisateurs (une vingtaine ?) en base de données.  
Aucune vraie difficulté, à part peut être le cryptage du mot de passe. 
Mais pour voir comment faire, tu peux aller sur le controller ```src/Controller/RegistrationController.php```.

![Later](https://i.ytimg.com/vi/wiHYx9NX4DM/maxresdefault.jpg)

#### Correction
Tu noteras que j'ai ajouté un utilisateur avec le rôle ```ROLE_ADMIN```. Il nous servira plus tard.

```shell
php bin/console make:fixtures

The class name of the fixtures to create (e.g. AppFixtures):
> UserFixtures
```

Dans __src/DataFixtures/UserFixtures.php__ :
```php
<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->passwordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();

        $lastname = $faker->lastName();
        $firstname = $faker->firstName();
        $pseudo = $this->createPseudo($lastname, $firstname);

        $user = new User();
        $user
            ->setEmail('admin@email.com')
            ->setFirstName($firstname)
            ->setLastName($lastname)
            ->setNickname($pseudo)
            ->setRoles(['ROLE_ADMIN'])
        ;
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        for ($i=0; $i < 20; $i++) {
            $lastname = $faker->lastName();
            $firstname = $faker->firstName();
            $pseudo = $this->createPseudo($lastname, $firstname);            ;
            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setFirstName($firstname)
                ->setLastName($lastname)
                ->setNickname($pseudo)
            ;
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function createPseudo(string ...$concat): string
    {
        $pseudo = '';
        foreach ($concat as $key => $value) {
            $pseudo .= substr($value, 0, 3);
        }
        $pseudo = strtolower($pseudo);
        return $pseudo;
    }
}
```
Remplis la base de données :
```shell
php bin/console doctrine:fixtures:load
```

## Récupérer l'utilisateur avec Twig
Dans les templates twig, tu peux conditionner l'affichage de certains éléments si l'utilisateur est connecté ou non.
Pour récupérer un utilisateur dans les templates, tu peux tout simplement taper :
```php
{{ app.user }} {# permet de récupèrer l'objet user #}
{{ app.user.firstName }} {# permet de récupérer la propriété firtName de l'objet user #}
```

Dans le template du menu __templates/\_inc/nav.html.twig__ je te propose d'écrire :
```php
{{ dump(app.user) }}
``` 
et de voir le résultat en étant déconnecté et connecté. 
Lorsque que l'utilisateur est déconnecté, la valeur ```null``` devrait être affichée.
À l'inverse, si l'utilisateur est connecté, l'objet devrait être affiché.

Pour tester si l'utilisateur est connecté ou non, il suffit de taper :
```php
{% if app.user %}
    Connecté
{% else %}
    Anonyme
{% endif %}
```

### Exercice
Je te laisse essayer d'afficher les liens de login et d'enregistrement si l'utilisateur est anonyme (non connecté) et inversement, le lien de logout si l'utilisateur est connecté.

![Later](https://i.ytimg.com/vi/lnVus5kklX0/maxresdefault.jpg)

#### Correction
Voici ma proposition : dans __templates/\_inc/nav.html.twig__, au niveau du dropdown :
```php
<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
    {% if app.user %}
        <li>
            <a class="dropdown-item" href="{{ path('app_logout') }}">Logout</a>
        </li>
    {% else %}
        <li>
            <a class="dropdown-item" href="{{ path('app_login') }}">Login</a>
        </li>
        <li class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ path('app_register') }}">Register</a>
        </li>
    {% endif %}
</ul>
```

## Conclusion

Comme tu peux le voir, il est facile de créer un système d'authentification basique. 
Trois lignes de commandes suffisent :
```shell
php bin/console make:user
php bin/console make:auth
php bin/console make:register-form
```

![easy](https://media.giphy.com/media/3o7btNa0RUYa5E7iiQ/giphy.gif)
