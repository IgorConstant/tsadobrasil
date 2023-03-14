<?php

namespace App\Controllers;
use App\Models\Video;
use Slim\Http\UploadedFile;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class VideoController extends Controller
{    
    
    const currentPage = 'videos';	
    const currentTitle = 'Vídeos';	

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('nome');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Video::orderBy('id','DESC');

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

        Video::create([
            'nome' => $request->getParam('nome'),
            'codigo' => $request->getParam('codigo'),
            'status' => 'A'
        ]);

        $this->container->flash->addMessage('success','Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        
    }

    public function edit($request, $response, $params)
    {
        $item = Video::where('id',$params['id'])->first();

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

        $item = Video::find($params['id']);


        if($error_file != 0 or $error_file_banner != 0)
        {           
            $this->container->flash->addMessage('error','Erro ao enviar o imagem!');
        }
        //END - ADD IMAGEM

        $item->nome = $request->getParam('nome');
        $item->codigo = $request->getParam('codigo');

        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Video::find($params['id']);

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

