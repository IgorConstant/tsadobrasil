<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\AuthUserMiddleware;

//ROTA SITE

$app->get('/','PageController:index')->Setname('home');
$app->get('/en','PageController:index')->Setname('home_en');


$app->get('/empresa','PageController:empresa')->Setname('empresa');

$app->get('/produtos','ProdutoController:site')->Setname('produtos');
$app->get('/produtos/{montadora}/{codigo}/{id}','ProdutoController:siteDetalhes')->Setname('produtos_detalhes');

$app->get('/linha/{id}','ProdutoController:linha')->Setname('produtos_linhas');

$app->get('/resultado','ProdutoController:busca')->Setname('busca');

$app->get('/onde-encontrar[/{id}]','RepresentanteController:site')->Setname('encontrar');

$app->get('/area-tecnica','PageController:tecnica')->Setname('tecnica');

$app->get('/downloads','PageController:downloads')->Setname('downloads');
$app->post('/downloads/send','PageController:downloadsSend')->Setname('downloads_send');
$app->get('/downloads/sucesso','PageController:downloadsSucesso')->Setname('downloads_sucesso');

$app->get('/contato','PageController:contato')->Setname('contato');
$app->post('/contato/send','PageController:contatoSend')->Setname('contato_send');
$app->get('/contato/sucesso','PageController:contatoSucesso')->Setname('contato_sucesso');
$app->post('/trabalhe/send','PageController:trabalheSend')->Setname('trabalhe_send');
$app->get('/trabalhe/sucesso','PageController:trabalheSucesso')->Setname('trabalhe_sucesso');


//AJAX
$app->get('/veiculos/ajax/{id}','VeiculoController:ajax');
$app->get('/modelos/ajax/{id}','ModeloController:ajax');
$app->post('/newsletter/ajax','NewsletterController:ajax')->Setname('newsletter_ajax');;


//ROTA Área user
$app->map(['GET','POST'],'/area-cliente/login','AuthClienteController:login')->Setname('areacliente_login');
$app->post('/area-cliente/add','AuthClienteController:register')->Setname('areacliente_add');

$app->group('/area-cliente', function($app){
    $app->get('','PageController:areacliente')->Setname('areacliente');
    $app->get('/catalogos','PageController:areacliente_catalogos')->Setname('areacliente_catalogos');
    $app->get('/folders','PageController:areacliente_folders')->Setname('areacliente_folders');
    $app->get('/videos','PageController:areacliente_videos')->Setname('areacliente_videos');
    $app->get('/logout','AuthClienteController:logout')->setName('areacliente_logout');  
})->add(new AuthUserMiddleware($container));
//ROTA ADMIN


$app->map(['GET','POST'],'/sac/login','AuthController:login')->Setname('app.login');

$app->get('/sac','DashboardController:index')->Setname('app.home')->add(new AuthMiddleware($container));
$app->group('/sac', function($app){
    $app->get('/','DashboardController:index')->Setname('app.home');
    
    $app->get('/logout','AuthController:logout')->setName('app.logout');   

    $app->group('/produtos', function($app){
        $app->get('','ProdutoController:index')->Setname('app.produtos');
        $app->get('/list:number','ProdutoController:index')->Setname('app.produtos');
        $app->map(['GET','POST'],'/add','ProdutoController:create')->Setname('app.produtos.create');    
        $app->get('/del/{id}','ProdutoController:delete')->Setname('app.produtos.del');
        $app->get('/edit/{id}','ProdutoController:edit')->Setname('app.produtos.edit');
        $app->post('/edit/{id}','ProdutoController:update')->Setname('app.produtos.update');
        $app->get('/catalogo/del/{id}','ProdutoController:CatalogoDelete')->Setname('app.produtos.delCatalogo');

        $app->get('/veiculos/{id}','VeiculoController:AddAjax')->Setname('app.produtos.veiculos');
        $app->get('/modelos/{id}','ModeloController:ajax')->Setname('app.produtos.modelos');
    });

    $app->group('/importar', function($app){
        $app->get('','ImportarController:index')->Setname('app.importar');
        $app->map(['POST'],'/add','ImportarController:create')->Setname('app.importar.create');    
    });

    $app->group('/linhas', function($app){
        $app->get('','LinhaController:index')->Setname('app.linhas'); 
        $app->map(['GET','POST'],'/add','LinhaController:create')->Setname('app.linhas.create');    
        $app->get('/del/{id}','LinhaController:delete')->Setname('app.linhas.del');
        $app->get('/edit/{id}','LinhaController:edit')->Setname('app.linhas.edit');
        $app->post('/edit/{id}','LinhaController:update')->Setname('app.linhas.update');
    });
   
    $app->group('/montadoras', function($app){
        $app->get('','MontadoraController:index')->Setname('app.montadoras'); 
        $app->map(['GET','POST'],'/add','MontadoraController:create')->Setname('app.montadoras.create');    
        $app->get('/del/{id}','MontadoraController:delete')->Setname('app.montadoras.del');
        $app->get('/edit/{id}','MontadoraController:edit')->Setname('app.montadoras.edit');
        $app->post('/edit/{id}','MontadoraController:update')->Setname('app.montadoras.update');
    });

    $app->group('/veiculos', function($app){
        $app->get('','VeiculoController:index')->Setname('app.veiculos'); 
        $app->map(['GET','POST'],'/add','VeiculoController:create')->Setname('app.veiculos.create');    
        $app->get('/del/{id}','VeiculoController:delete')->Setname('app.veiculos.del');
        $app->get('/edit/{id}','VeiculoController:edit')->Setname('app.veiculos.edit');
        $app->post('/edit/{id}','VeiculoController:update')->Setname('app.veiculos.update');
    });

    $app->group('/modelos', function($app){
        $app->get('','ModeloController:index')->Setname('app.modelos'); 
        $app->map(['GET','POST'],'/add','ModeloController:create')->Setname('app.modelos.create');    
        $app->get('/del/{id}','ModeloController:delete')->Setname('app.modelos.del');
        $app->get('/edit/{id}','ModeloController:edit')->Setname('app.modelos.edit');
        $app->post('/edit/{id}','ModeloController:update')->Setname('app.modelos.update');
    });

    $app->group('/clientes', function($app){
        $app->get('','ClienteController:index')->Setname('app.clientes'); 
        $app->map(['GET','POST'],'/add','ClienteController:create')->Setname('app.clientes.create');    
        $app->get('/del/{id}','ClienteController:delete')->Setname('app.clientes.del');
        $app->get('/edit/{id}','ClienteController:edit')->Setname('app.clientes.edit');
        $app->post('/edit/{id}','ClienteController:update')->Setname('app.clientes.update');
    });

    $app->group('/representantes', function($app){
        $app->get('','RepresentanteController:index')->Setname('app.representantes'); 
        $app->map(['GET','POST'],'/add','RepresentanteController:create')->Setname('app.representantes.create');    
        $app->get('/del/{id}','RepresentanteController:delete')->Setname('app.representantes.del');
        $app->get('/edit/{id}','RepresentanteController:edit')->Setname('app.representantes.edit');
        $app->post('/edit/{id}','RepresentanteController:update')->Setname('app.representantes.update');
    });

    $app->group('/folders', function($app){
        $app->get('','FolderController:index')->Setname('app.folders'); 
        $app->map(['GET','POST'],'/add','FolderController:create')->Setname('app.folders.create');    
        $app->get('/del/{id}','FolderController:delete')->Setname('app.folders.del');
        $app->get('/edit/{id}','FolderController:edit')->Setname('app.folders.edit');
        $app->post('/edit/{id}','FolderController:update')->Setname('app.folders.update');
    });

    $app->group('/catalogos', function($app){
        $app->get('','CatalogoController:index')->Setname('app.catalogos'); 
        $app->map(['GET','POST'],'/add','CatalogoController:create')->Setname('app.catalogos.create');    
        $app->get('/del/{id}','CatalogoController:delete')->Setname('app.catalogos.del');
        $app->get('/edit/{id}','CatalogoController:edit')->Setname('app.catalogos.edit');
        $app->post('/edit/{id}','CatalogoController:update')->Setname('app.catalogos.update');
    });

    $app->group('/newsletter', function($app){
        $app->get('','NewsletterController:index')->Setname('app.newsletter'); 
        $app->get('/del/{id}','NewsletterController:delete')->Setname('app.newsletter.del');
        $app->get('/edit/{id}','NewsletterController:edit')->Setname('app.newsletter.edit');
        $app->post('/edit/{id}','NewsletterController:update')->Setname('app.newsletter.update');        
        $app->get('/export','NewsletterController:export')->Setname('app.newsletter.export');
    });

    $app->group('/videos', function($app){
        $app->get('','VideoController:index')->Setname('app.videos'); 
        $app->map(['GET','POST'],'/add','VideoController:create')->Setname('app.videos.create');    
        $app->get('/del/{id}','VideoController:delete')->Setname('app.videos.del');
        $app->get('/edit/{id}','VideoController:edit')->Setname('app.videos.edit');
        $app->post('/edit/{id}','VideoController:update')->Setname('app.videos.update');
    });
  
    
})->add(new AuthMiddleware($container));
//

// $app->group('/app', function($app){
//     $app->map(['GET','POST'],'/login','AuthController:login')->Setname('auth.login');
//     $app->map(['GET','POST'],'/register','AuthController:register')->Setname('auth.register');
//     $app->get('/confirmacao', 'AuthController:confirmation');
//     $app->get('/reenviar', 'AuthController:resend')->setName('auth.resend');
    
// });

//$app->get('/sac/logout','AuthController:logout')->setName('logout');
