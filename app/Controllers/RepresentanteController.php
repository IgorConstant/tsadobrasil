<?php

namespace App\Controllers;
use App\Models\Representante;
use App\Models\Estado;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class RepresentanteController extends Controller
{    
    
    const currentPage = 'representantes';	
    const currentTitle = 'Representantes';	

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('nome','cidade');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Representante::orderBy('nome','ASC');

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

        Representante::create([
            'nome' => $request->getParam('nome'),
            'nome_url' => Helper::url($request->getParam('cidade').' '.$request->getParam('estado')),
            'contato' => $request->getParam('contato'),
            'documento' => $request->getParam('documento'),
            'cep' => $request->getParam('cep'),
            'endereco' => $request->getParam('endereco'),
            'numero' => $request->getParam('numero'),
            'bairro' => $request->getParam('bairro'),
            'cidade' => $request->getParam('cidade'),
            'estado' => $request->getParam('estado'),
            'referencia' => $request->getParam('referencia'),
            'telefone' => $request->getParam('telefone'),
            'whatsapp' => $request->getParam('whatsapp'),
            'email' => $request->getParam('email'),
            'googlemaps' => $request->getParam('googlemaps'),
            'status' => 'A'
        ]);


        $this->container->flash->addMessage('success','Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

        
    }

    public function edit($request, $response, $params)
    {
        
        $item = Representante::where('id',$params['id'])->first();

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

        $item = Representante::find($params['id']);

        $item->nome = $request->getParam('nome');
        $item->nome_url = Helper::url($request->getParam('cidade').' '.$request->getParam('estado'));
        $item->contato = $request->getParam('contato');
        $item->documento = $request->getParam('documento');
        $item->cep = $request->getParam('cep');
        $item->endereco = $request->getParam('endereco');
        $item->numero = $request->getParam('numero');
        $item->bairro = $request->getParam('bairro');
        $item->cidade = $request->getParam('cidade');
        $item->estado = $request->getParam('estado');
        $item->referencia = $request->getParam('referencia');
        $item->telefone = $request->getParam('telefone');
        $item->whatsapp = $request->getParam('whatsapp');
        $item->email = $request->getParam('email');
        $item->googlemaps = $request->getParam('googlemaps');
        $item->status = 'A';

        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Representante::find($params['id']);

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
  
    public function site($request, $response, $params)
    {
        
        if(!$params){
            $representantes = Representante::where('estado','SP')->get();
        }else{
            $representantes = Representante::where('estado',$params['id'])->get();
        }

        $data = [
                'd' => $representantes
                ];
        

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/encontrar'.$language['file'].'.twig',$data);
    }
  

}