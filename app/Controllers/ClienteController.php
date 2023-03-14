<?php

namespace App\Controllers;
use App\Models\Cliente;
use App\Models\Estado;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class ClienteController extends Controller
{    
    
    const currentPage = 'clientes';	
    const currentTitle = 'Clientes';	

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('nome','email');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Cliente::orderBy('nome','ASC');

        if($filtro)
        {
            $itens = $itens->Filter($filtro, $filtro_campos);
        }

        $filtros = array('filtro'=>$filtro);

        $itens = $itens->Paginates(array('limite'=>$this->configs['pagesLimit'],'page'=>$pageCurrent));
        $pagesLink = Helper::getPaginate(array('current'=>$pageCurrent,'pages'=>$itens->lastPage(),'links'=>$getFilters)); 

        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'data' =>  $itens,
            'pagesLink' =>  $pagesLink,
            'pageCurrent' =>  $pageCurrent,
            'filtros' => $filtros,
            'getFilters' => $getFilters,
        ];

        return $this->container->view->render($response, 'admin/'.self::currentPage.'/index.twig',$data);

    }
    
    public function create($request, $response)
    {

        if($request->isGet())
        {
            $data = [
                'page' => self::currentPage,
                'title' => self::currentTitle,
                'estados' => Estado::all()->sortBy("estado")
            ];

            return $this->container->view->render($response, 'admin/'.self::currentPage.'/create.twig',$data);
        }

        Cliente::create([
            'nome' => $request->getParam('nome'),
            'email' => $request->getParam('email'),
            'tipo' => $request->getParam('tipo'),
            'cpf' => $request->getParam('cpf'),
            'cnpj' => $request->getParam('cnpj'),
            'boleto_codigo' => $request->getParam('boleto_codigo'),
            'senha' => $request->getParam('senha'),
            'senhamd5' => password_hash($request->getParam('senha'), PASSWORD_DEFAULT),
            'status' => 'A'
        ]);


        $this->container->flash->addMessage('success','Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

        
    }

    public function edit($request, $response, $params)
    {
        
        $item = Cliente::where('id',$params['id'])->first();

        if(!$item)
        {
            $this->container->flash->addMessage('error','Registro não encontrado');
            return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        }
    
        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'd' => $item,
            'estados' => Estado::all()->sortBy("estado")
        ];
        

        return $this->container->view->render($response, 'admin/'.self::currentPage.'/edit.twig', $data);
    }

    public function update($request, $response, $params)
    {

        $item = Cliente::find($params['id']);

        $item->nome = $request->getParam('nome');
        $item->email = $request->getParam('email');
        $item->tipo = $request->getParam('tipo');
        $item->cpf = $request->getParam('cpf');
        $item->cnpj = $request->getParam('cnpj');
        $item->boleto_codigo = $request->getParam('boleto_codigo');
        $item->senha = $request->getParam('senha');
        $item->senhamd5 = password_hash($request->getParam('senha'), PASSWORD_DEFAULT);
        $item->status = 'A';

        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Cliente::find($params['id']);

        if($item)
        {
            $item->delete();
            $this->container->flash->addMessage('success','Exclusão realizada com sucesso!');
        }
        else
        {
            $this->container->flash->addMessage('error','Erro ao excluir!');
        }

        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }
  

  

}