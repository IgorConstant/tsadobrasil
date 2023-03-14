<?php

namespace App\Controllers;
use App\Models\Newsletter;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;

class NewsletterController extends Controller
{    
    
    const currentPage = 'newsletter';	
    const currentTitle = 'Newsletter';	

    public function index($request, $response)
    {
        
        $filtro = $request->getParam('filtro');
        $filtro_campos = array('nome','email','telefone');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $pageCurrent = (int)$request->getParam('page');
        $itens = Newsletter::orderBy('id','DESC');

        if($filtro)
        {
            $itens = $itens->Filter($filtro, $filtro_campos);
        }        

        $itens = $itens->Paginates(array('limite'=>$this->configs['pagesLimit'],'page'=>$pageCurrent));
        $pagesLink = Helper::getPaginate(array('current'=>$pageCurrent,'pages'=>$itens->lastPage(),'links'=>$getFilters));       

        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'data' =>  $itens,
            'pagesLink' =>  $pagesLink,
            'getFilters' => $getFilters,
        ];

        return $this->container->view->render($response, 'admin/'.self::currentPage.'/index.twig',$data);

    }
    

    public function edit($request, $response, $params)
    {
        $item = Newsletter::where('id',$params['id'])->first();

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

        $item = Newsletter::find($params['id']);

        $item->email = $request->getParam('email');
        $item->telefone = $request->getParam('telefone');
        $item->save();

        $this->container->flash->addMessage('success','Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

    }

    public function delete($request, $response, $params)
    {
        
        $item = Newsletter::find($params['id']);

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

    public function ajax($request, $response)
    {   

        if(!$request->getParam('email') or !$request->getParam('telefone')){
            return 'empty';
        }

        //Verifica se já tem cadastrado
        $item = Newsletter::where('email',$request->getParam('email'))->first();
        if($item){
            return 'error';
        }

        Newsletter::create([
            'email' => $request->getParam('email'),
            'telefone' => $request->getParam('telefone'),
            'status' => 'A'
        ]);
        
        return 'sucesso';
        
    }

    public function export($request, $response)
    {      
        
        $filtro = $request->getParam('filtro');
        $filtro_campos = array('nome','email','telefone');
        $getFilters = Helper::getFilters(array('filtro'=>$filtro));

        $itens = Newsletter::orderBy('id','DESC');

        if($filtro)
        {
            $itens = $itens->Filter($filtro, $filtro_campos);
        } 

        $itens = $itens->get();

        
        // Criamos uma tabela HTML com o formato da planilha
        $html = '';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td style="border: 1px solid #D0D7E5"><b>E-MAIL</b></td>';
        $html .= '<td style="border: 1px solid #D0D7E5"><b>TELEFONE</b></td>';     
        $html .= '<td style="border: 1px solid #D0D7E5"><b>DATA</b></td>';     
        $html .= '</tr>';
        $html .= '</tr>';

        foreach ($itens as $key => $v)
        {
            $html .= '<tr>';    
            $html .= '<td style="border: 1px solid #D0D7E5">'.$v->email.'</td>';
            $html .= '<td style="border: 1px solid #D0D7E5">'.$v->telefone.'</td>';
            $html .= '<td style="border: 1px solid #D0D7E5">'.date('d/m/Y', strtotime($v->created_at)).'</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
       
        $arquivo = self::currentPage.'_'.date('d-m-Y').'.xls';
        
        // Configurações header para forçar o download
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
        header ("Content-Description: Newsletter Trippyz" );

        echo $html;
    }

}

