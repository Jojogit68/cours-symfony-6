# Localisation des annonces

> Les documentations seront tes meilleures amies si tu souhaites progresser. Il faut essayer de les comprendre et ne pas en avoir peur !
> Je t'invite donc pas à prendre à chaque fois un moment pour lire les liens qui sont proposés dans le cours.
> (à commencer par [RTFM](https://fr.wikipedia.org/wiki/RTFM_%28expression%29), qui est une expression que tu entendras sûrement un jour si tu ne lis pas les documentations).

Dans ce chapitre, nous allons voir comment ajouter une carte sur le site, permettant de localiser les annonces. Pour localiser des lieux sur une carte, rien de tel que d'utiliser les coordonnées géographique : la latitude et la longitude ! Par exemple, la Zone 51 se trouve prêt de Coyote Springs, située aux coordonnées 36.81783500071307, -114.93324954942855.

- La première étape consistera à ajouter à la classe annonce les champs permettant stocker les coordonnées géographique

- Ensuite, lors de la création d'uns annonce, nous allons interroger l'[API Adresse (Base Adresse Nationale - BAN) - api.gouv.fr](https://api.gouv.fr/les-api/base-adresse-nationale) qui nous permettra de récupérer les coordonnées géographique à partir d'un adresse.

- Enfin nous utiliserons la librairie [Leaflet](https://leafletjs.com/) pour afficher les annonces sur une carte.

## Ajouter les champs de coordonnées

D'abord, posons la question : quel type de champs pour stocker la latitude et la longitude ? Pour cela, analysons une latitude et une longitude.

- Latitude : __36.81783500071307__. Valeur comprise entre -90 et +90 degrés, 2 chiffres avant la virgule. 6 chiffres après la virgules permettent une précision au mètre prêt. Nous avons donc 8 chiffres au total;

- Longitude : __-114.93324954942855__. Valeur comprise entre -180 et +180 degrés, 3 chiffres avant la virgule. 6 chiffres après la virgules permettent une précision au mètre prêt. Nous avons donc 9 chiffres au total;

Il va donc falloir créer 2 champs de type *float* avec un *scale* (chiffre après la virgule en MySql) de 6 et une *precision* (nombre total de chiffre) de 8 pour la latitude et de 9 pour la longitude.

C'est partie :

```shell
php bin/console make:entity Annonce

 Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > lat

 Field type (enter ? to see all types) [string]:
 > decimal

 Precision (total number of digits stored: 100.00 would be 5) [10]:
 > 8

 Scale (number of decimals to store: 100.00 would be 2) [0]:
 > 4

 Can this field be null in the database (nullable) (yes/no) [no]:
 >

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > lng

 Field type (enter ? to see all types) [string]:
 > decimal

 Precision (total number of digits stored: 100.00 would be 5) [10]:
 > 9                                                                                                                                                                                                                

 Scale (number of decimals to store: 100.00 would be 2) [0]:
 > 4

 Can this field be null in the database (nullable) (yes/no) [no]:
 >

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 >



  Success! 


 Next: When you're ready, create a migration with php bin/console make:migration
```

Ajoutons aussi des champs pour l'adresse tant qu'à faire :

```shell
php bin/console make:entity Annonce

 Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > street

 Field type (enter ? to see all types) [string]:
 >

 Field length [255]:
 >

 Can this field be null in the database (nullable) (yes/no) [no]:
 >

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > postcode

 Field type (enter ? to see all types) [string]:
 >

 Field length [255]:
 >

 Can this field be null in the database (nullable) (yes/no) [no]:
 >

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > city

 Field type (enter ? to see all types) [string]:
 >

 Field length [255]:
 >

 Can this field be null in the database (nullable) (yes/no) [no]:
 >

 updated: src/Entity/Annonce.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 >



  Success! 


 Next: When you're ready, create a migration with php bin/console make:migration
```

Avant de générer la migration, vas voir ce que Symfony à généré dans l'entité __Annonce__ :

```shell
#[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 4)]
private ?string $lat = null;

#[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 4)]
private ?string $lng = null;
```

Génères la migrations avec `php bin/console m:mi`, vérifies que celle ci est correct et si oui, mets à jour la base de données avec `php bin/console d:m:m`.

Tu peux ajouter les champs suivants au formulaire de création et d'édition d'une annonce (c'est le fichier __/src/Form/AnnonceType.php__ pour rappel) :

```php
 ->add('address', null, ['label' => 'address', 'mapped' => false]) // ce champ permettra de rechercher l'adresse, il n'existe pas dans l'entité, alors il doit comporter l'option mapped => false
 ->add('street', null, ['label' => 'street'])
 ->add('postcode', null, ['label' => 'postcode'])
 ->add('city', null, ['label' => 'city'])
 ->add('lat') // ce champ sera caché
 ->add('lng') // ce champ sera caché
```

## API Adresse

L'idée est que quand un utilisateur tape une adresse dans le champ si bien nommé _address_, une liste d'adresse apparaîtra sous ce champ et il pourra choisir l'adresse qui lui convient. Lorsqu'il cliquera sur un élément de la liste, le système complétera les champs _street_, _postcode_, _city_, _lat_, et _lng_. Quand il soumettre le formulaire, nous aurons toutes les coordonnées qui s'enregistreront en base de données !

### Comment fonctionne l'API Adresse ?

Première chose à faire : [aller sur la doc](https://adresse.data.gouv.fr/api-doc/adresse) ! 

Que peut-on retenir ? 

- cette API ne répond qu'au verbe HTTP __GET__ (récupérer). 

- Le _endpoint_ (l'url de l'API) est `https://api-adresse.data.gouv.fr` 

- La ressource pour rechercher une adresse est `/search/`

- Le paramètre de recherche est définit grâce à `?q=` et la recherche doit ressembler à ceci `8+bd+du+port`

- Il est possible d'ajouter d'autres paramètres tels que
  
  - le code postal avec `&postcode=44380`
  
  - le type avec `&type=street`
  
  - la latitude et la longitude avec `&lat=48.789&lon=2.789`
  
  - l'auto-complétion avec `&autocomplete=0`
  
  - et la limite avec `&autocomplete=0`

Mit bout à bout voici à quoi pourrait ressembler une requête :

`https://api-adresse.data.gouv.fr/search/?q=8+bd+du+port&limit=15`

Tu peux tester facilement cette API en utilisant un logiciel tel que [Insomnia](https://insomnia.rest/download) ou [Postman](https://www.postman.com/) ou encore plus simplement en rentrant cette url dans un navigateur 🐒.

La réponse de l'API sera au format JSON respectant la spec [GeoCodeJSON](https://github.com/geocoders/geocodejson-spec).

### JavaScript pour appeler l'API

Dès qu'un utilisateur tape une adresse dans le champ adresse, nous allons faire un appel à l'API avec les informations renseignées par l'utilisateur. Tous va se passer côté navigateur, il nous donc utiliser JavaScript.

Vu que nous n'aurons besoin de cette fonctionnalité d'auto-complétion seulement sur certaines pages de l'application, notamment dans l'ajout et l'édition d'une annonce, nous n'allons charger les fichiers JavaScript nécessaire seulement sur les pages concernées. Pour cela, il faut ajouter un nouveau point d'entrée.

Crées deux fichiers :

- un fichier __assets/js/autoCompleteAddress.js__ avec le contenu
  
  ```javascript
  const autoCompleteAddress = () => {
      console.log('autoCompleteAddress OK')
  }
  
  export default autoCompleteAddress
  ```

- et un fichier __assets/formAnnonce.js__ avec le contenu 
  
  ```javascript
  import autoCompleteAddress from "./js/autoCompleteAddress"
  ```

Puis ajoutes une nouvelle entrée dans la configuration de Webpack (c'est le fichier __webpack.config.js__ pour rappel)

```javascript
.addEntry('formAnnonce', './assets/formAnnonce.js')
```

Il ne reste plus qu'à lier les fichiers qui seront compilés dans les templates __templates/annonce/edit.html.twig__ et __templates/annonce/new.html.twig__

```twig
{% block javascripts %}
    {{ parent() }}
    {{ encore_entry_script_tags('formAnnonce') }}
{% endblock %}
```

Tu peux lancer la commande `npm run watch` et laisser le terminal ouvert. Les fichiers seront compilés à chaque enregistrement. Vas sur la page de création d'une annonce et ouvres la console. Tu devrais voir dans la console `autoCompleteAddress OK`.

#### Exercice

Je te laisse essayer de récupérer les coordonnées géographiques à partir de l'adresse renseignée par l'utilisateur.

![](\\wsl.localhost\Ubuntu\home\vodoo\devenv\cours-symfo-v2\autocomplete.gif)

 Pour ce faire, voici les étapes à suivre et ce dont tu auras besoin :

- Sélectionner le champ (input) permettant de renseigner l'adresse avec [document.querySelector - Référence Web API | MDN](https://developer.mozilla.org/fr/docs/Web/API/Document/querySelector);

- écouter l'événement `keyup` de cet élément, si bien que lorsque que l'utilisateur tape une lettre, une action sera exécuté (un `console.log` par exemple)[EventTarget.addEventListener() - Référence Web API | MDN](https://developer.mozilla.org/fr/docs/Web/API/EventTarget/addEventListener);

- lorsque que l'utilisateur tape une lettre, envoyer une requête GET grâce à [Fetch - Référence Web API | MDN](https://developer.mozilla.org/fr/docs/Web/API/Fetch_API/Using_Fetch). N'hésites pas à faire un `console.log` du résultat pour voir comment parcourir l'objet reçu;

- lorsque l'API répond, construire une liste de `li` avec les données des adresses récupérés avec [document.createElement - Référence Web API | MDN](https://developer.mozilla.org/fr/docs/Web/API/Document/createElement) et afficher cette liste sous le champ de recherche avec [Element.after() - Web APIs | MDN](https://developer.mozilla.org/en-US/docs/Web/API/Element/after);

- sur chaque `li`, écouter l'événement `click` si bien que lorsque l'utilisateur clique sur un élément `li`, les champs rue, code postale, ville, latitude et longitude soient remplie avec les données de l'adresse sélectionnée.

![](https://static.wikia.nocookie.net/spongebob/images/8/86/Just_in_Time_for_Christmas_154.png/revision/latest?cb=20211205013207)

##### Correction

Voici une proposition de correction :

Dans le fichier __assets/js/autoCompleteAddress.js__ :

```javascript
const endpoint = new URL('https://api-adresse.data.gouv.fr/search/')

const autoCompleteAddress = (fieldSelector, onChoose) => {
    const searchElement = document.querySelector(fieldSelector)
    const resultContainer = createResultContainer()
    searchElement.after(resultContainer)
    let timer = null

    searchElement.addEventListener('keyup', (e) => {
        if (timer) {
            clearTimeout(timer)
        }

        if (e.target.value.length < 4) {
            resultContainer.innerHTML = ''
            return
        }

        if (e.keyCode === 16) {
            return
        }

        timer = setTimeout(() => {
            const userQuery = e.target.value.trim().replaceAll(' ', '+')
            search(userQuery).then(data => {
                resultContainer.innerHTML = ''
                data.features.forEach(address => {
                    const li = document.createElement('li')
                    li.classList.add('list-group-item')
                    li.innerText = address.properties.label
                    li.addEventListener('click', () => {
                        resultContainer.innerHTML = ''
                        searchElement.value = address.properties.label
                        onChoose(address)
                    })
                    resultContainer.appendChild(li)
                })
            })
        }, 500)
    })
}

const search = (query) => {
    endpoint.searchParams.set('q', query)
    endpoint.searchParams.set('autocomplete', '1')
    return fetch(endpoint).then(r => r.json())
}

const createResultContainer = () => {
    const resultContainer = document.createElement('ul')
    resultContainer.classList.add('list-group')
    resultContainer.style.position = 'absolute'
    return resultContainer
}

export default autoCompleteAddress
```

Puis dans le fichier __assets/formAnnonce.js__ :

```javascript
import autoCompleteAddress from "./js/autoCompleteAddress"

autoCompleteAddress('#annonce_address', address => {
    document.querySelector('#annonce_street').value = address.properties.name
    document.querySelector('#annonce_postcode').value = address.properties.postcode
    document.querySelector('#annonce_city').value = address.properties.city
    document.querySelector('#annonce_lat').value = address.geometry.coordinates[1]
    document.querySelector('#annonce_lng').value = address.geometry.coordinates[0]
})
```