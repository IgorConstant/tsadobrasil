<?php

namespace App\Controllers;
use App\Models\Catalogo;
use Slim\Http\UploadedFile;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class CatalogoController extends Controller
{    
    
    const currentPage = 'catalogos';	
    const currentTitle = 'Catálogos';	

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('nome');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Catalogo::orderBy('id','DESC');

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
            ];

            return $this->container->view->render($response, 'admin/'.self::currentPage.'/create.twig',$data);
        }

        $directory = $this->container->upload_directory.'/'.self::currentPage;
        $arquivo = $request->getUploadedFiles()['arquivo'];
        if($arquivo->GetError()){
            $this->container->flash->addMessage('error','Erro ao enviar o arquivo!');
        }else{
            $arquivo_name = $this->moveUploadFile($directory, $arquivo); 
        }

        Catalogo::create([
            'nome' => $request->getParam('nome'),
            'arquivo' => $arquivo_name,
            'status' => 'A'
        ]);

        $this->container->flash->addMessage('success','Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        
    }

    public function edit($request, $response, $params)
    {
        $item = Catalogo::where('id',$params['id'])->first();

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

        $item = Catalogo::find($params['id']);

        $directory = $this->container->upload_directory.'/'.self::currentPage;

        //ADD IMAGEM
        $error_file = 0;

        $arquivo = $request->getUploadedFiles()['arquivo'];
        if($imagem->file){
            if($imagem->GetError()){
                $error_file = 1;                
            }else{
                //Verifica se tem imagem
                if($item->arquivo!="")
                {	
                    unlink($directory.'/'.$item->arquivo);
                }                
                $arquivo_name = $this->moveUploadFile($directory, $arquivo);
            }            
        }

        if($error_file != 0)
        {           
            $this->container->flash->addMessage('error','Erro ao enviar o arquivo!');
        }
        //END - ADD IMAGEM

        $item->nome = $request->getParam('nome');

        if($imagem->file){
            $item->arquivo = $arquivo_name;
        }

        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Catalogo::find($params['id']);

        if($item)
        {   
            //Exluir fotos
            $directory = $this->container->upload_directory.'/'.self::currentPage;

            if($item->arquivo!="")
            {	
                unlink($directory.'/'.$item->arquivo);
            }
            
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

