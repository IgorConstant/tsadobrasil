<?php

namespace App\Controllers;
use App\Models\Modelo;
use Slim\Http\UploadedFile;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class ModeloController extends Controller
{    
    
    const currentPage = 'modelos';	
    const currentTitle = 'Modelos';	

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('modelo');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Modelo::orderBy('modelo','ASC');

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
                'configs' => $this->configs
            ];

            return $this->container->view->render($response, 'admin/'.self::currentPage.'/create.twig',$data);
        }

        Modelo::create([
            'veiculo_id' => $request->getParam('veiculo'),
            'modelo' => $request->getParam('modelo'),
            'modelo_url' => Helper::url($request->getParam('modelo')),
            'status' => 'A'
        ]);

        $this->container->flash->addMessage('success','Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        
    }

    public function edit($request, $response, $params)
    {
        $item = Modelo::where('id',$params['id'])->first();

        if(!$item)
        {
            $this->container->flash->addMessage('error','Registro não encontrado');
            return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        }
    
        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'configs' => $this->configs,
            'd' => $item,
        ];
        

        return $this->container->view->render($response, 'admin/'.self::currentPage.'/edit.twig', $data);
    }

    public function update($request, $response, $params)
    {

        $item = Modelo::find($params['id']);


        if($error_file != 0 or $error_file_banner != 0)
        {           
            $this->container->flash->addMessage('error','Erro ao enviar o imagem!');
        }
        //END - ADD IMAGEM

        $item->veiculo_id = $request->getParam('veiculo');
        $item->modelo = $request->getParam('modelo');
        $item->modelo_url = Helper::url($request->getParam('modelo'));

        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Modelo::find($params['id']);

        //Verifica se tem uma marca relacionada
        if($item->produtos()->count() > 0)
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

    public function ajax($request, $response, $params)
    {
        
        $item = Modelo::where('veiculo_id',$params['id'])->orderBy('modelo', 'ASC')->get();        
      
        if($item->count() == 0)
        {
            $retorn = '<option value="">nenhum modelo encontrado</option>';
        }
        else
        {
            $retorn = '<option value="" disabled selected>Selecione o modelo</option>';
            foreach ($item as $key => $v)
            {
                $retorn .= '<option value="'.$v->id.'">'.$v->modelo.'</option>';
            }
        }

        echo $retorn;
                
    }



}

