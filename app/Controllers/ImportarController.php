<?php

namespace App\Controllers;
use App\Models\Produto;
use App\Models\ProdutoFoto;
use App\Models\ProdutoVeiculo;
use App\Models\Montadora;
use App\Models\Veiculo;
use App\Models\Modelo;
use App\Models\Linha;
use App\Helpers\Helpers as Helper;

class ImportarController extends Controller
{    
    
    const currentPage = 'importar';	
    const currentTitle = 'Importar';

    public function index($request, $response)
    {
        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'data' => ''
        ];

        return $this->container->view->render($response, 'admin/'.self::currentPage.'/index.twig',$data);
    }


    public function create($request, $response)
    {


        // $produtos  = Produto::all();

        // foreach($produtos as $p){

        //     echo  $p->id;
        //     $foto = ProdutoFoto::create([
        //         'produto_id' => $p->id,
        //         'imagem' => $p->codigo_tsa.'.jpg',
        //         'ordem' => '1',
        //         ]);
        // }


        // exit;
        
        //UPLOAD
        $directory = $this->container->upload_directory.'/produtos/csv';

        $arquivo = $request->getUploadedFiles()['arquivo'];

        if($arquivo->GetError()){
            $this->container->flash->addMessage('error','Erro ao enviar o imagem!');
        }else{
            $filename_arquivo = $this->moveUploadFile($directory, $arquivo); 
        }


        $arquivo = $directory.'/'.$filename_arquivo;

        if (($handle = fopen($arquivo, "r")) !== FALSE) {
            $i = 0;
            
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                
                if($i > 1){

                //vericar se é produto inteiro ou parcial
                    
                    if($data['2']!=''){

                        //acha a linha
                        $codigo = substr($data['2'], 0, 4);
                        $linha = Linha::where('codigo','like',$codigo)->first();                        
                        if($linha){
                            $linha_id = $linha->id;
                        }else{
                            $linha_id = '10';
                        }
                        
      
                        //Verifica se tem a montadora, se não tiver cadastra
                        if($data['9']!=''){
                            $montadora = Montadora::where('montadora',$data['9'])->first();                        
                            if(!$montadora){
                                $montadora = Montadora::create([
                                                            'montadora' => $data['9'],
                                                            'montadora_url' => Helper::url($data['9']),
                                                            'status' => 'A',
                                                            ]);
                            }
                        }

                        //ADD PRODUTO INTEIRO
                        $produto = Produto::create([
                                        'linha_id' => $linha_id,
                                        'montadora_id' => $montadora->id,
                                        'codigo_tsa' => $data['2'],
                                        'codigo_tsa_url' => Helper::url($data['2']),
                                        'codigo_original' => $data['3'],
                                        'codigo_outros' => $data['4'],
                                        'codigo_oculto' => $data['5'],
                                        'compativel' => $data['6'],
                                        'valor_ohmico' => $data['7'],
                                        'observacoes' => ($data['8']),
                                        'relacionados' => $data['15'],
                                        'keywords' => Helper::keywords($data['2'].' '.$data['3'].' '.$data['4'].' '.$data['5'].' '.$data['6'].' '.$data['15']),
                                        'status' => 'A',
                                        ]);
                                        
                                        
                        //ADD PRODUTO PARCIAL
                        $ProdutoImagem = ProdutoFoto::create([
                                                            'produto_id' => $produto->id,
                                                            'imagem' => $data['2'].'.jpg',
                                                            'ordem' => '1'
                                                            ]);


                        //Verifica se tem a veiculo + id montadora, se não tiver cadastra
                        if($data['10']!=''){
                            $veiculo = Veiculo::where('montadora_id',$montadora->id)->where('veiculo',$data['10'])->first();                        
                            if(!$veiculo){
                                $veiculo = Veiculo::create([
                                                            'montadora_id' => $montadora->id,
                                                            'veiculo' => $data['10'],
                                                            'veiculo_url' => Helper::url($data['10']),
                                                            'status' => 'A',
                                                            ]);
                            }
                            $veiculo_id = $veiculo->id;
                        }else{
                            $veiculo_id = null;
                        }

                        //Verifica se tem o modelo + id veiculo, se não tiver cadastra
                        if($data['11']!=''){
                            $modelo = Modelo::where('veiculo_id',$veiculo->id)->where('modelo',$data['11'])->first();                        
                            if(!$modelo){
                                $modelo = Modelo::create([
                                                        'veiculo_id' => $veiculo->id,
                                                        'modelo' => $data['11'],
                                                        'modelo_url' => Helper::url($data['11']),
                                                        'status' => 'A',
                                                        ]);
                            }
                            $modelo_id = $modelo->id;
                        }else{
                            $modelo_id = null;
                        }

                        //funcao ano

                        //ADD PRODUTO PARCIAL
                        $ProdutoVeiculo = ProdutoVeiculo::create([
                                                                'produto_id' => $produto->id,
                                                                'montadora' => $montadora->montadora,
                                                                'montadora_id' => $montadora->id,
                                                                'veiculo' => $data['10'],
                                                                'veiculo_id' => $veiculo_id,
                                                                'modelo' => $data['11'],
                                                                'modelo_id' =>  $modelo_id,
                                                                'motor' => $data['12'],
                                                                'ano' => $data['13'],
                                                                'combustivel' => ($data['14']),
                                                                ]);

                        //ADD PRODUTO PARCIAL
                        // $prouto_relacionado = array(
                        //     'codigo_tsa' => $data['15'],
                        //     );                        

                    }else{

                        $ProdutoVeiculo = ProdutoVeiculo::create([
                                                                'produto_id' => $produto->id,
                                                                'montadora' => $montadora->modelo,
                                                                'montadora_id' => $montadora->id,
                                                                'veiculo' => utf8_encode($data['10']),
                                                                'veiculo_id' => $veiculo_id,
                                                                'modelo' => utf8_encode($data['11']),
                                                                'modelo_id' =>  $modelo_id,
                                                                'motor' => $data['12'],
                                                                'ano' => $data['13'],
                                                                'combustivel' => utf8_encode($data['14']),
                                                                ]);


                    }
                
                
                }

                $i++;
            }

            //DELETA XML            
            unlink($arquivo);             

            $this->container->flash->addMessage('success','Importação realizada do sucesso!');
            return $response->withRedirect($this->container->router->pathFor('app.produtos'));
 
        }else{

            echo 'erro arquivo';
            exit;
            $this->container->flash->addMessage('error','Falha ao abrir arquivo');
            return $response->withRedirect($this->container->router->pathFor('app.'.self::currentPage));

        }


    }
}