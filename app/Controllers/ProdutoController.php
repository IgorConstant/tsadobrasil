<?php

namespace App\Controllers;

use App\Models\Produto;
use App\Models\ProdutoFoto;
use App\Models\ProdutoRelacionado;
use App\Models\ProdutoVeiculo;
use App\Models\Veiculo;
use App\Models\Modelo;
use App\Models\Linha;
use Respect\Validation\Validator as v;
use App\Helpers\Helpers as Helper;
use App\Helpers\ResizeImg as Resize;

class ProdutoController extends Controller
{

    const currentPage = 'produtos';
    const currentTitle = 'Produtos';

    public function index($request, $response)
    {

        $filtro = $request->getParam('filtro');
        $filtro_campos = array('codigo_tsa', 'codigo_outros');

        $filtro_linha = $request->getParam('linha');
        $filtro_montadora = $request->getParam('montadora');
        $getFilters = Helper::getFilters(array('filtro' => $filtro, 'linha' => $filtro_linha, 'montadora' => $filtro_montadora));

        $pageCurrent = (int)$request->getParam('page');


        $itens = Produto::with('linha')->with('montadora')->with('veiculos');

        if ($filtro) {
            $itens = $itens->Filter($filtro, $filtro_campos);
        }

        if ($filtro_linha) {
            $itens = $itens->where('linha_id', $filtro_linha);
        }
        if ($filtro_montadora) {
            $itens = $itens->where('montadora_id', $filtro_montadora);
        }


        $filtros = array('filtro' => $filtro, 'linha' => $filtro_linha, 'montadora' => $filtro_montadora);

        $itens = $itens->Paginates(array('limite' => $this->configs['pagesLimit'], 'page' => $pageCurrent));
        $pagesLink = Helper::getPaginate(array('current' => $pageCurrent, 'pages' => $itens->lastPage(), 'links' => $getFilters));

        //gantidade paginas
        //Pagina final
        //Pagina atual       

        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'data' =>  $itens,
            'configs' => $this->configs,
            'pagesLink' =>  $pagesLink,
            'pageCurrent' =>  $pageCurrent,
            'filtros' => $filtros,
            'getFilters' => $getFilters,
        ];

        return $this->container->view->render($response, 'admin/' . self::currentPage . '/index.twig', $data);
    }

    public function create($request, $response)
    {

        if ($request->isGet()) {
            $data = [
                'page' => self::currentPage,
                'title' => self::currentTitle,
                'configs' => $this->configs
            ];

            return $this->container->view->render($response, 'admin/' . self::currentPage . '/create.twig', $data);
        }

        //ADD CATALOGO e PLANTA
        $directory = $this->container->upload_directory;

        $arquivo = $request->getUploadedFiles()['arquivo'];
        $arquivo_lamina = $request->getUploadedFiles()['arquivo_lamina'];

        if ($arquivo->GetError() or $arquivo_lamina->GetError()) {
            $this->container->flash->addMessage('error', 'Erro ao enviar o imagem!');
        } else {
            $arquivo_name = $this->moveUploadFile($directory . '/produtos/catalogos/', $arquivo);
            $arquivo_lamina_name = $this->moveUploadFile($directory . '/produtos/catalogos/', $arquivo_lamina);
        }
        //END - ADD CATALOGO e PLANTA

        $item = Produto::create([
            'linha_id' => $request->getParam('linha'),
            'montadora_id' => $request->getParam('montadora'),
            'codigo_tsa' => $request->getParam('codigo_tsa'),
            'codigo_original' => $request->getParam('codigo_original'),
            'codigo_outros' => $request->getParam('codigo_outros'),
            'codigo_oculto' => $request->getParam('codigo_oculto'),
            'compativel' => $request->getParam('compativel'),
            'valor_ohmico' => $request->getParam('valor_ohmico'),
            'observacoes' => $request->getParam('observacoes'),
            'relacionados' => $request->getParam('relacionados'),
            'destaque' => $request->getParam('destaque'),
            'status' => $request->getParam('status'),
            'video' => $request->getParam('id_video'),
            'arquivo_manual' => $arquivo_name,
            'arquivo_lamina' => $arquivo_lamina_name,
            'keywords' => Helper::keywords($request->getParam('codigo_tsa') . ' ' . $request->getParam('codigo_original') . ' ' . $request->getParam('codigo_outros') . ' ' . $request->getParam('codigo_oculto') . ' ' . $request->getParam('compativel') . ' ' . $request->getParam('relacionados')),
        ]);


        for ($i = 0; $i < count($request->getParam('veiculo')); $i++) {
            ProdutoVeiculo::create([
                'produto_id' => $item->id,
                'montadora_id' => $request->getParam('montadora'),
                'veiculo_id' => ($request->getParam('veiculo')[$i]) ? $request->getParam('veiculo')[$i] : '0',
                'modelo_id' => ($request->getParam('modelo')[$i]) ? $request->getParam('modelo')[$i] : '0',
                'motor' => $request->getParam('motor')[$i],
                'ano' => $request->getParam('ano')[$i],
                'combustivel' => $request->getParam('combustivel')[$i],
            ]);
        }



        //ADD FOTOS
        $fotos = $request->getUploadedFiles()['fotos'];
        foreach ($fotos as $key => $v) {
            if ($v->file != "") {
                $directory_fotos = $directory . '/produtos';
                $foto = $this->moveUploadFile($directory_fotos, $v);
                Resize::TamanhoAutoFotoMax($directory_fotos . '/' . $foto, '1000', '1000');
                $dataFoto = null;
                $dataFoto = array(
                    'imagem' => $foto,
                    'ordem' => $key + 1,
                );
                $item->fotos()->create($dataFoto);
            }
        }
        //END - ADD FOTOS


        $this->container->flash->addMessage('success', 'Cadastro realizado com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.' . self::currentPage));
    }

    public function edit($request, $response, $params)
    {
        $item = Produto::where('id', $params['id'])->with('veiculos')
            ->with(['fotos' => function ($query) {
                $query->orderBy('ordem', 'ASC');
            }])->first();


        if (!$item) {
            $this->container->flash->addMessage('error', 'Registro não encontrado');
            return $response->withRedirect($this->container->router->pathFor('app.' . self::currentPage));
        }

        //Fotos
        $fotos = array();
        foreach ($item->fotos as $key => $v) {
            $fotos[] = array(
                'name' => $v->imagem,
                'type' => 'image/jpeg',
                'file' => '../../../assets/uploads/produtos/' . $v->imagem,
                'local' => '../../../assets/uploads/produtos/' . $v->imagem,
            );
        }

        $lista_fotos = json_encode($fotos);

        $veiculos = Veiculo::where('montadora_id', $item->montadora_id)->orderby('veiculo', 'ASC')->get();
        $modelos = Modelo::orderby('modelo', 'ASC')->get();

        $data = [
            'page' => self::currentPage,
            'title' => self::currentTitle,
            'configs' => $this->configs,
            'veiculos' => $veiculos,
            'modelos' => $modelos,
            'd' => $item,
            'fotos' => $lista_fotos
        ];


        return $this->container->view->render($response, 'admin/' . self::currentPage . '/edit.twig', $data);
    }

    public function update($request, $response, $params)
    {

        $item = Produto::find($params['id']);

        $directory = $this->container->upload_directory . '/';

        //ADD CATALOGO e PLANTA
        $error_file = 0;

        $arquivo = $request->getUploadedFiles()['arquivo'];
        if ($arquivo->file) {
            if ($arquivo->GetError()) {
                $error_file = 1;
            } else {
                //Verifica se tem registro
                if ($item->arquivo != "") {
                    unlink($directory . '/produtos/catalogos/' . $item->arquivo_manual);
                }
                $arquivo_name = $this->moveUploadFile($directory . 'produtos/catalogos', $arquivo);
            }
        }

        $arquivo_lamina = $request->getUploadedFiles()['arquivo_lamina'];
        if ($arquivo_lamina->file) {
            if ($arquivo_lamina->GetError()) {
                $error_file = 1;
            } else {
                //Verifica se tem registro
                if ($item->arquivo_lamina != "") {
                    unlink($directory . '/produtos/catalogos/' . $item->arquivo_lamina);
                }
                $arquivo_lamina_name = $this->moveUploadFile($directory . '/produtos/catalogos', $arquivo_lamina);
            }
        }

        if ($error_file != 0) {
            $this->container->flash->addMessage('error', 'Erro ao enviar o imagem!');
        }
        //END - ADD CATALOGO e PLANTA

        $item->linha_id = $request->getParam('linha');
        $item->montadora_id = $request->getParam('montadora');
        $item->codigo_tsa = $request->getParam('codigo_tsa');
        $item->codigo_original = $request->getParam('codigo_original');
        $item->codigo_outros = $request->getParam('codigo_outros');
        $item->codigo_oculto = $request->getParam('codigo_oculto');
        $item->compativel = $request->getParam('compativel');
        $item->valor_ohmico = $request->getParam('valor_ohmico');
        $item->observacoes = $request->getParam('observacoes');
        $item->relacionados = $request->getParam('relacionados');
        $item->video = $request->getParam('id_video');
        $item->destaque = $request->getParam('destaque');
        $item->status = $request->getParam('status');
        $item->keywords = Helper::keywords($request->getParam('codigo_tsa') . ' ' . $request->getParam('codigo_original') . ' ' . $request->getParam('codigo_outros') . ' ' . $request->getParam('codigo_oculto') . ' ' . $request->getParam('compativel') . ' ' . $request->getParam('relacionados'));


        if ($arquivo->file) {
            $item->arquivo_manual = $arquivo_name;
        }
        if ($arquivo_lamina->file) {
            $item->arquivo_lamina = $arquivo_lamina_name;
        }

        $item->save();

        $item->veiculos()->delete();

        for ($i = 0; $i < count($request->getParam('veiculo')); $i++) {
            ProdutoVeiculo::create([
                'produto_id' => $item->id,
                'montadora_id' => $request->getParam('montadora'),
                'veiculo_id' => ($request->getParam('veiculo')[$i]) ? $request->getParam('veiculo')[$i] : '0',
                'modelo_id' => ($request->getParam('modelo')[$i]) ? $request->getParam('modelo')[$i] : '0',
                'motor' => $request->getParam('motor')[$i],
                'ano' => $request->getParam('ano')[$i],
                'combustivel' => $request->getParam('combustivel')[$i],
            ]);
        }


        //ORDEM DAS FOTOS
        $fotos = $request->getUploadedFiles()['fotos'];


        $ordem_array = json_decode($request->getParam('fileuploader-list-fotos'), true);

        $sort = null;
        for ($i = 0; $i < count($ordem_array); $i++) {
            $arquivo = @end(explode('/', $ordem_array[$i]['file']));
            $ordem = $ordem_array[$i]['index'] + 1;
            $sort[$arquivo]['arquivo'] = $arquivo;
            $sort[$arquivo]['ordem'] = $ordem;
        }


        //UPLOAD NOVAS FOTOS
        $i = 0;
        foreach ($fotos as $key => $v) {
            if ($v->file != "") {

                $directory_produto = $directory . '/produtos';
                $foto = $this->moveUploadFile($directory_produto, $v);
                Resize::TamanhoAutoFotoMax($directory_produto . '/' . $foto, '1000', '1000');
                $dataFoto = null;
                $dataFoto = array(
                    'imagem' => $foto,
                    'ordem' => $key + 1,
                );

                $item->fotos()->create($dataFoto);

                //verifica se tem no array de ordem
                if (array_key_exists($v->getClientFilename(), $sort) == 1) {
                    $sort[$v->getClientFilename()]['arquivo'] = $foto;
                }
            }
        } //UPLOAD NOVAS FOTOS - FIM

        //ORDER ATUALIZAR
        foreach ($sort as $v => $r) {
            $arquivo = $r['arquivo'];
            $ordem = $r['ordem'];
            ProdutoFoto::where('produto_id', $item->id)->where('imagem', $arquivo)->update(['ordem' => $ordem]);
        }

        //DELETA FOTOS
        $except = array();;
        foreach ($sort as $v => $r) {
            $except[] = $r['arquivo'];
        }

        //LISTA DADOS
        $itemFotos = ProdutoFoto::where('produto_id', $item->id);
        $itemFotos = $itemFotos->whereNotIn('imagem', $except)->get();

        //DELETE
        $ids_del = array();
        foreach ($itemFotos as $key => $v) {
            $ids_del[] = $v->id;
            @unlink($directory . '/produtos/' . $v->imagem);
        }

        //EXCLUI DO BANCO
        if ($ids_del != null) {
            $item->fotos()->whereIn('id', $ids_del)->delete();
        }
        //DELETA FOTOS - FIM

        $this->container->flash->addMessage('success', 'Atualização realizada com sucesso');
        return $response->withRedirect($this->container->router->pathFor('app.' . self::currentPage));
    }

    public function delete($request, $response, $params)
    {

        $item = Produto::where('id', $params['id'])->with('fotos')->first();

        if ($item) {

            //Exluir fotos
            $directory = $this->container->upload_directory;

            if ($item->imagem != "") {
                unlink($directory . '/produtos/destaques/' . $item->imagem);
            }


            $item->fotos()->delete();
            $item->veiculos()->delete();
            $item->delete();

            $this->container->flash->addMessage('success', 'Exclusão realizada com sucesso!');
        } else {
            $this->container->flash->addMessage('error', 'Erro ao excluir!');
        }

        return $response->withRedirect($this->container->router->pathFor('app.' . self::currentPage));
    }

    public function site($request, $response)
    {

        //add produto no array linhas
        foreach ($this->configs['linhas'] as $r) {
            $itens = Produto::where('linha_id', $r->id)->with('montadora')->orderby('id', 'ASC')->limit('10')
                ->with(['fotos' => function ($query) {
                    $query->orderBy('ordem', 'ASC')->limit('1');
                }])->get();
            $r['produto'] = $itens;
        }

        $data = [
            'configs' => $this->configs
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response, 'site/produtos' . $language['file'] . '.twig', $data);
    }

    public function siteDetalhes($request, $response, $params)
    {

        $item = Produto::where('id', $params['id'])->with('veiculos')
            ->with(['fotos' => function ($query) {
                $query->orderBy('ordem', 'ASC');
            }])->first();

        $codigos = @preg_split("/\\r\\n|\\r|\\n/", $item->codigo_original);
        $codigos_outros = @preg_split("/\\r\\n|\\r|\\n/", $item->codigo_outros);

        function valor_tipo($valores)
        {

            $valores_new;

            foreach ($valores as $v) {
                if (str_contains($v, 'Cheio: ')) {
                    $valores_new[] = array('tipo' => 'cheio', 'valor' => str_replace('Cheio: ', '', $v));
                } elseif (str_contains($v, 'Meio: ')) {
                    $valores_new[] = array('tipo' => 'meio', 'valor' => str_replace('Meio: ', '', $v));
                } elseif (str_contains($v, 'Vazio:')) {
                    $valores_new[] = array('tipo' => 'vazio', 'valor' => str_replace('Vazio: ', '', $v));
                }
            }

            return  $valores_new;
        }


        $valores = valor_tipo(@preg_split("/\\r\\n|\\r|\\n/", $item->valor_ohmico));

        $codigos_relacionados = @preg_split("/\\r\\n|\\r|\\n/", $item->relacionados);
        $relacionados =  Produto::whereIN('codigo_tsa', $codigos_relacionados)->get();

        $data = [
            'd' => $item,
            'codigos' => $codigos,
            'codigos_outros' => $codigos_outros,
            'valores' => $valores,
            'relacionados' => $relacionados,
            'configs' => $this->configs
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response, 'site/produtos_detalhes' . $language['file'] . '.twig', $data);
    }

    public function linha($request, $response, $params)
    {

        //get id
        $linha = Linha::where('linha_url', $params)->first();

        $pageCurrent = (int)$request->getParam('page');

        $itens = Produto::where('linha_id', $linha->id)->with('montadora')->orderby('id', 'ASC')
            ->with(['fotos' => function ($query) {
                $query->orderBy('ordem', 'ASC');
            }]);

        $itens = $itens->Paginates(array('limite' => $this->configs['pagesLimit'], 'page' => $pageCurrent));
        $pagesLink = Helper::getPaginateSite(array('current' => $pageCurrent, 'pages' => $itens->lastPage(), 'links' => '', 'url' => 'tsa/linha/' . $params['id']));


        $data = [
            'd' => $itens,
            'linha' => $linha->linha,
            'configs' => $this->configs,
            'pagesLink' =>  $pagesLink,
            'pageCurrent' =>  $pageCurrent,
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response, 'site/linhas' . $language['file'] . '.twig', $data);
    }

    public function busca($request, $response, $params)
    {

        $pageCurrent = (int)$request->getParam('page');

        $filtro = $request->getParam('q');
        $filtro_montadora = $request->getParam('montadora');
        $filtro_veiculo = $request->getParam('veiculo');
        $filtro_modelo = $request->getParam('modelo');
        $filtro_linha = $request->getParam('linha');

        $getFilters = Helper::getFilters(array('q' => $filtro, 'montadora' => $filtro_montadora, 'veiculo' => $filtro_veiculo, 'modelo' => $filtro_modelo, 'linha' => $filtro_linha));

        $results = Produto::with('montadora')->orderby('id', 'ASC')->limit('20')->orderBy('id', 'DESC')
            ->with(['fotos' => function ($query) {
                $query->orderBy('ordem', 'ASC');
            }]);

        if ($filtro != '') {
            $results = $results->orWhere('codigo_tsa', 'like', '%' . $filtro . '%')
                ->orWhere('codigo_original', 'like', '%' . $filtro . '%')
                ->orWhere('codigo_outros', 'like', '%' . $filtro . '%')
                ->orWhere('codigo_oculto', 'like', '%' . $filtro . '%')
                ->orWhere('keywords', 'like', '%' . $filtro . '%')
                ->orWhereHas('montadora', function ($q) use ($filtro) {
                    $q->where('montadora', 'LIKE', '%' . $filtro . '%');
                })
                ->orWhereHas('veiculos.veiculo', function ($q) use ($filtro) {
                    $q->where('veiculo', 'LIKE', '%' . $filtro . '%');
                });
        }
        if ($filtro_montadora != '') {
            $results = $results->where('montadora_id', $request->getParam('montadora'));
        }
        if ($filtro_veiculo != '') {

            $vei = $request->getParam('veiculo');

            $results = $results->whereHas('veiculos', function ($query) use ($vei) {
                return $query->where('veiculo_id', $vei);
            });
        }
        if ($filtro_modelo != '') {

            $mol = $request->getParam('modelo');

            $results = $results->whereHas('veiculos', function ($query) use ($mol) {
                return $query->where('modelo_id', $mol);
            });
        }
        if ($filtro_linha != '') {
            $results = $results->where('linha_id', $request->getParam('linha'));
        }


        $results = $results->Paginates(array('limite' => $this->configs['pagesLimit'], 'page' => $pageCurrent)); //$this->configs['pagesLimit']
        $pagesLink = Helper::getPaginateSite(array('current' => $pageCurrent, 'pages' => $results->lastPage(), 'links' => $getFilters, 'url' => 'tsa/resultado'));

        $data = [
            'results' => $results,
            'configs' => $this->configs,
            'pagesLink' =>  $pagesLink,
            'pageCurrent' =>  $pageCurrent,
        ];

        $language = $this->language($request->getUri()->getPath());

        return $this->container->view->render($response, 'site/resultados' . $language['file'] . '.twig', $data);
    }
}
