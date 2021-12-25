# PeanutRouter
## A router manager in PHP

## Usage
Create a file .htaccess like this
```apacheconf
RewriteEngine on

RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA]
```

Create a file index.php like this
```php
session_start();

require dirname(__DIR__, 1) . "../vendor/autoload.php";
require __DIR__ . "/controllers/Site.php";

use Luizfnunes\PeanutRouter\Router;
use example\Site;

# Configuration of URL, 
# Define the BASE_URL and the DEEPTH_BASE_URL
# 0 => (127.0.0.1), 1 => (peanut-router), 2 => (example)
$baseUrl = 'http://127.0.0.1/peanut-router/example';
$deepthUrl = 2;

# Instance of Router
$router = new Router($baseUrl, $deepthUrl);

# Adding custom pattern to url
$router->addPattern('{phone}', '/^[0-9]{3}-[0-9]{4}$/');

# Define the routes
$router->get('/', [Site::class, 'index']);
$router->get('/error', [Site::class, 'error']);
$router->get('/products', [Site::class, 'productShow']);

# Route with other http methods
$router->post('/product/new', [Site::class, 'productNew']);
$router->put('/product/update', [Site::class, 'productOthers']);
$router->patch('/product/patch', [Site::class, 'productOthers']);
$router->delete('/product/delete', [Site::class, 'productOthers']);

# Routes with parameters
$router->get('/id/{number}', [Site::class, 'withNumber']);
$router->get('/name/{string}', [Site::class, 'withString']);
$router->get('/post/{stringx}', [Site::class, 'withStringAndSpecial']);
$router->get('/show/{lower}/{number}', [Site::class, 'withTwoParams']);

# Route with custom parameter
$router->get('/client/{phone}', [Site::class, 'withCustom']);

# Run the route system
$router->run();

# Verify the router errors
if($router->hasError()){
    $_SESSION['errors'] = [];
    foreach($router->getErrors() as $errors){
        $_SESSION['errors'][] = $errors;
    }
    # Redirect route
    $router->redirect('/error');
}
```

For more information, see the example folder.
