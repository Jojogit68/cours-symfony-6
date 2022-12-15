# Memo
## Routes
### Définir une route
```php
    /**
     * @Route(
     *     "/articles/{_locale}/{year}/{slug}.{_format}",
     *     defaults={"_format": "html"},
     *     requirements={
     *         "_locale": "en|fr",
     *         "_format": "html|rss",
     *         "year": "\d+"
     *     }
     * )
     */
    public function show($_locale = 'fr', $year, $slug)
    {
    }
```
### Générer une route
```php
$this->router->generate('blog', array(
    'page' => 2,
    'category' => 'Symfony',
));
// ou 
$url = $this->generateUrl(
            'blog_show',
            array('slug' => 'my-blog-post')
        );
```

### Générer une route dans un template
```html
<a href="{{ path('route.name', {id: entity.id, slug: entity.slug}) }}">View</a>
```

## Request
```php
<?php
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

// l' URI requêtée (par exemple /about) et les paramètres
$request->getPathInfo();

// récupèrer les variables $_GET
$request->query->get('id');

// récupèrer les variables $_POST 
$request->request->get('category', 'default category');

// récupèrer les variables $_SERVER
$request->server->get('HTTP_HOST');

// récupèrer l'instance de UploadedFile identifié par "attachment"
$request->files->get('attachment');

// récupèrer une valeur de $_COOKIE
$request->cookies->get('PHPSESSID');

```
### Autres méthodes utiles de Request
```php
    /**
     * l' URI requêtée (par exemple /about)
     * pas d'équivalent direct en PHP
     */     
    $request->getPathInfo(); 

    /**
     * par exemple GET, POST, PUT, DELETE ou HEAD
     * $_SERVER['REQUEST_METHOD']
     */
    $request->getMethod();

    /**
     * est que c'est la méthode POST ?
     * $_SERVER['REQUEST_METHOD'] === 'POST'
     */
    $request->isMethod('POST');

    /**
     * un tableau des languages que le client accepte
     * $_SERVER['HTTP_ACCEPT_LANGUAGE']
     */
    $request->getLanguages();

    /**
     * est ce que c'est du HTTPS ?
     * pas d'équivalent direct en PHP
     */
    $request->isSecure();

    /**
     * est-ce une requête Ajax ? 
     *  pas d'équivalent direct en PHP, mais pourrait être :
     * !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
     */
    $request->isXmlHttpRequest(); 

    /**
     * récupérer les headers HTTP de la requête
     */ 
    $request->headers->get('host');
    $request->headers->get('content_type');
```

## Entité
### Chercher un objet en DB
```php
$repository = $this->getDoctrine()->getRepository(Product::class);

// look for a single Product by its primary key (usually "id")
$product = $repository->find($id);

// look for a single Product by name
$product = $repository->findOneBy(['name' => 'Keyboard']);
// or find by name and price
$product = $repository->findOneBy([
    'name' => 'Keyboard',
    'price' => 19.99,
]);

// look for multiple Product objects matching the name, ordered by price
$products = $repository->findBy(
    ['name' => 'Keyboard'],
    ['price' => 'ASC']
);

// look for *all* Product objects
$products = $repository->findAll();
```
### Persister un objet en DB
```php
// you can fetch the EntityManager via $this->getDoctrine()
// or you can add an argument to your action: index(EntityManagerInterface $em)
$em = $this->getDoctrine()->getManager();

$product = new Product();
$product->setName('Keyboard');
$product->setPrice(19.99);
$product->setDescription('Ergonomic and stylish!');

// tell Doctrine you want to (eventually) save the Product (no queries yet)
$em->persist($product);

// actually executes the queries (i.e. the INSERT query)
$em->flush();
```

