<?php

namespace App\Controllers;

use App\Models\Produto;
use App\Models\Video;
use App\Models\Folder;
use App\Models\Catalogo;
use App\Helpers\Helpers as Helper;


class PageController extends Controller
{    
    public function index($request, $response)
    {

        $language = $this->language($request->getUri()->getPath());

        $pdotudos = Produto::where('destaque','S')->where('status','A')
                            ->with(['fotos' => function ($query) {
                                $query->orderBy('ordem','ASC');
                            }])->get();

        $data = [            
            'destaques' => $pdotudos,
            'configs' => $this->configs
        ];
        
        return $this->container->view->render($response,'site/index'.$language['file'].'.twig',$data);
    }

    public function empresa($request, $response)
    {
        $data = [            
           
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/empresa'.$language['file'].'.twig',$data);
    }    

    public function tecnica($request, $response)
    {
        $data = [            
           
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/tecnica'.$language['file'].'.twig',$data);
    }
    
    public function downloads($request, $response)
    {
        $data = [            
           
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/downloads'.$language['file'].'.twig',$data);
    }

    public function downloadsSend($request, $response)
    {

        $nome = $request->getParam('nome');
        $email = $request->getParam('email');
        $telefone = $request->getParam('telefone');
        $tipo = $request->getParam('tipo');

        $payload = [
            'to_name' => $this->container->appName,
            'to_email' => $this->container->appEmail,
            'd' => array(
                        'data'=> date('d/m/Y'),
                        'nome'=> $nome,
                        'email'=> $email,
                        'telefone'=> $telefone,
                        'tipo'=> $tipo,
                        )
        ];

        $this->container->mail->send($payload,'downloads.twig','Download - Site');

        return $response->withRedirect($this->container->router->pathFor('downloads_sucesso'));
    }

    public function downloadsSucesso($request, $response)
    {
        $data = [            
            'configs' => $this->configs,
        ];

        return $this->container->view->render($response,'site/downloads_sucesso.twig',$data);
    }

    public function contato($request, $response)
    {
        $data = [            
           
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/contato'.$language['file'].'.twig',$data);
    }

    public function contatoSend($request, $response)
    {

        $nome = $request->getParam('nome');
        $telefone = $request->getParam('telefone');
        $email = $request->getParam('email');
        $assunto = $request->getParam('assunto');
        $estado = $request->getParam('estado');
        $cidade = $request->getParam('cidade');
        $mensagem = $request->getParam('mensagem');

        $payload = [
            'to_name' => $this->container->appName,
            'to_email' => $this->container->appEmail,
            'd' => array(
                        'data'=> date('d/m/Y'),
                        'nome'=> $nome,
                        'email'=> $email,
                        'telefone'=> $telefone,
                        'assunto'=> $assunto,
                        'estado'=> $estado,
                        'cidade'=> $cidade,
                        'mensagem'=> $mensagem,
                        )
        ];

        $this->container->mail->send($payload,'contato.twig','Contato - Site');

        return $response->withRedirect($this->container->router->pathFor('contato_sucesso'));
    }

    public function contatoSucesso($request, $response)
    {
        $data = [            
            'configs' => $this->configs,
        ];

        return $this->container->view->render($response,'site/contato_sucesso.twig',$data);
    }

    public function trabalheSend($request, $response)
    {

        $nome = $request->getParam('nome');
        $telefone = $request->getParam('telefone');
        $email = $request->getParam('email');
        $mensagem = $request->getParam('mensagem');

        $arquivo = $_FILES['arquivo'];
  
        $payload = [
            'to_name' => $this->container->appName,
            'to_email' => $this->container->appEmail,
            'arquivo_temp' => $arquivo['tmp_name'],
            'arquivo_name' => $arquivo['name'],
            'd' => array(
                        'data'=> date('d/m/Y'),
                        'nome'=> $nome,
                        'email'=> $email,
                        'telefone'=> $telefone,
                        'mensagem'=> $mensagem,
                        )
        ];

        $this->container->mail->send($payload,'trabalhe.twig','Trabalhe Conosco - Site');

        return $response->withRedirect($this->container->router->pathFor('trabalhe_sucesso'));
    }

    public function trabalheSucesso($request, $response)
    {
        $data = [            
            'configs' => $this->configs,
        ];

        return $this->container->view->render($response,'site/trabalhe_sucesso.twig',$data);
    }

    public function areacliente($request, $response)
    {

        $data = [            
                'c' => $_SESSION['userCliente']
                ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/area-cliente'.$language['file'].'.twig',$data);
    }


    public function areacliente_videos($request, $response)
    {
        
        $itens = Video::where('status','A')->orderBy('id','DESC')->get();
        
        $data = [            
           'd' => $itens,
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/area-cliente-videos'.$language['file'].'.twig',$data);
    }

    public function areacliente_catalogos($request, $response)
    {
        
        $itens = Catalogo::where('status','A')->orderBy('id','DESC')->get();
        
        $data = [            
           'd' => $itens,
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/area-cliente-catalogos'.$language['file'].'.twig',$data);
    }

    public function areacliente_folders($request, $response)
    {
        
        $itens = Folder::where('status','A')->orderBy('id','DESC')->get();
        
        $data = [            
           'd' => $itens,
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response,'site/area-cliente-folders'.$language['file'].'.twig',$data);
    }

   

}