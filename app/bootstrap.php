<?php

use Slim\Views\Twig;
use Slim\Views\TwigExtension;

session_start();

date_default_timezone_set('America/Sao_Paulo');

require __DIR__.'/../vendor/autoload.php';

if ((strrpos(strtolower($_SERVER['SERVER_NAME']), "localhost") !== false)){   
    $db_host = 'localhost';
    $db_name = 'db_tsafinal';
    $db_user = 'root';
    $db_pass = '';
    $app_email  = "";
}else{
    // $db_host = 'localhost';
    // $db_name = '';
    // $db_user = '';
    // $db_pass = '';

    $db_host = 'localhost';
    $db_name = '';
    $db_user = '';
    $db_pass = '';

    $app_email  = "";
}

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => $db_host,
            'database' => $db_name,
            'username' => $db_user,
            'password' => $db_pass,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            ]
    ]
]);

$container = $app->getContainer();

//DADOS PADRÕES DO PROJETO
$container['appName'] = "TSA DO Brasil";
$container['appEmail'] = $app_email;

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['validator'] = function($container){
    return new App\Validation\Validator;
};

$container['flash'] = function($container){
    return new Slim\Flash\Messages;
};

$container['mail'] = function($container){
    return new App\Mail($container);
};

$container['auth'] = function($container){
    return new App\Auth\Auth($container);
};

$container['authUser'] = function($container){
    return new App\Auth\AuthUser($container);
};


$container['upload_directory'] = __DIR__ . '/../assets/uploads';

$container['view'] = function($container){
        $view = new Slim\Views\Twig(__DIR__ . '/../views', [
            'cache' => false,
        ]);

        $view->addExtension(new Slim\Views\TwigExtension(
            $container->router,
            $container->request->getUri()
        ));

        $view->getEnvironment()->addGlobal('flash', $container->flash);

        $view->getEnvironment()->addGlobal('auth',[
            'check' => $container->auth->check(),
            'user' => $container->auth->user(),
            'checkUser' => $container->authUser->checkUser(),
        ]);

        return $view;
};

require __DIR__.'/commons.php';

getControllers($container,[
                        'AuthController',
                        'AuthClienteController',
                        'UserController',
                        'DashboardController',                        
                        'ProdutoController',
                        'ImportarController',
                        'ClienteController',
                        'RepresentanteController',
                        'LinhaController',
                        'MontadoraController',
                        'VeiculoController',
                        'ModeloController',
                        'NewsletterController',
                        'VideoController',
                        'FolderController',
                        'CatalogoController',
                        'PageController',
                        ]);

$app->add(new App\Middleware\DisplayInputErrorsMiddleware($container));

require __DIR__.'/routes.php';
