<?php

namespace App\Controllers;
use App\Models\Montadora;
use App\Models\Linha;
use App\Models\Veiculo;
use Slim\Http\UploadedFile;

class Controller
{    
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
        $this->montadoras = Montadora::all(['id','montadora','montadora_url'])->sortBy("montadora");
        $this->linhas = Linha::all(['id','linha','linha_url'])->sortBy("linha");
        $this->veiculos = Veiculo::all(['id','veiculo','veiculo_url'])->sortBy("veiculo");

        
        $this->configs = array(
                            'montadoras' => $this->montadoras,
                            'linhas' => $this->linhas,
                            'veiculos' => $this->veiculos,
                            'pagesLimit' => '20',
                            );

    }

    protected function moveUploadFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(),PATHINFO_EXTENSION);
        $basename =  bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s',$basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR .$filename);

        return $filename;
    }

    protected function language($lg){	
        $lg = explode("/",$lg);
        if (in_array("en", $lg)) { 
            $version = array('version'=>'EN','file'=>'_en');
        }else{
            $version = array('version'=>'PT','file'=>'');
        }
        return $version;
    }

}