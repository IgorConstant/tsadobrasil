<?php

namespace App\Controllers;
use App\Models\Veiculo;
use Slim\Http\UploadedFile;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class VeiculoController extends Controller
{    
    
    const currentPage = 'veiculos';	
    const currentTitle = 'Veiculos';	

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('veiculo');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Veiculo::orderBy('veiculo','ASC');

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

        Veiculo::create([
            'montadora_id' => $request->getParam('montadora'),
            'veiculo' => $request->getParam('veiculo'),
            'veiculo_url' => Helper::url($request->getParam('veiculo')),
            'status' => 'A'
        ]);

        $this->container->flash->addMessage('success','Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));
        
    }

    public function edit($request, $response, $params)
    {
        $item = Veiculo::where('id',$params['id'])->first();

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

        $item = Veiculo::find($params['id']);


        if($error_file != 0 or $error_file_banner != 0)
        {           
            $this->container->flash->addMessage('error','Erro ao enviar o imagem!');
        }
        //END - ADD IMAGEM

        $item->montadora_id = $request->getParam('montadora');
        $item->veiculo = $request->getParam('veiculo');
        $item->veiculo_url = Helper::url($request->getParam('Veiculo'));

        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Veiculo::find($params['id']);

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
        
        $item = Veiculo::where('montadora_id',$params['id'])->orderBy('veiculo', 'ASC')->get();
        
        if($item->count() == 0)
        {
            $retorn = '<option value="">nenhum veículo encontrado</option>';
        }
        else
        {
            $retorn = '<option value="" disabled selected>Selecione o veículo</option>';
            foreach ($item as $key => $v)
            {
                $retorn .= '<option value="'.$v->id.'">'.$v->veiculo.'</option>';
            }
        }

        echo $retorn;

    }

    public function AddAjax($request, $response, $params)//Admin
    {       

        $item = Veiculo::where('montadora_id',$params['id'])->orderBy('veiculo', 'ASC')->get();

        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'configs' => $this->configs,
            'd' => $item,
            ];

        return $this->container->view->render($response, 'admin/produtos/veiculos.twig', $data);
    }

}

