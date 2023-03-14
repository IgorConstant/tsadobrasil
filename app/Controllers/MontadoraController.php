<?php

namespace App\Controllers;
use App\Models\Montadora;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class MontadoraController extends Controller
{    
    
    const currentPage = 'montadoras';	
    const currentTitle = 'Montadoras';	

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('montadora');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Montadora::orderBy('montadora','ASC');

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
                'title' => self::currentTitle
            ];

            return $this->container->view->render($response, 'admin/'.self::currentPage.'/create.twig',$data);
        }

        Montadora::create([
            'montadora' => $request->getParam('montadora'),
            'montadora_url' => Helper::url($request->getParam('montadora')),
            'status' => 'A'
        ]);

        $this->container->flash->addMessage('success','Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        
    }

    public function edit($request, $response, $params)
    {
        $item = Montadora::where('id',$params['id'])->first();

        if(!$item)
        {
            $this->container->flash->addMessage('error','Registro não encontrado');
            return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        }
    
        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'd' => $item,
        ];
        

        return $this->container->view->render($response, 'admin/'.self::currentPage.'/edit.twig', $data);
    }

    public function update($request, $response, $params)
    {

        $item = Montadora::find($params['id']);     

        $item->montadora = $request->getParam('montadora');
        $item->montadora_url = Helper::url($request->getParam('montadora'));

        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Montadora::find($params['id']);

        //Verifica se tem uma montadora relacionada
        if($item->veiculos()->count() > 0)
        {
            $this->container->flash->addMessage('error','Erro ao excluir! Existe algum registro relacionado a esse item.');
        }
        else
        {

            if($item)
            {   
                $item->delete();
                $this->container->flash->addMessage('success','Exclusão realizada com sucesso!');
            }
            else
            {
                $this->container->flash->addMessage('error','Erro ao excluir!');
            }

        }

        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function site($request, $response)
    {

        //echo Helper::upper('biscate');
        
        $data = [            
            'montadoras' => $this->montadoras,
            'dados' => Montadora::all()
        ];


        $language = $this->language($request->getUri()->getPath());
        
        return $this->container->view->render($response,'site/montadoras'.$language['file'].'.twig',$data);
    }

}

