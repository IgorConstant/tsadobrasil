<?php

namespace App\Helpers;

class ResizeImg
{

    //redimeciona automatico tamanho da foto (largura / altura proporcional)
	public static function TamanhoAutoFotoMax($img, $largura, $altura)
    {
	        
        $imagem_gerada = $img;
        $tipo = @end(explode(".",$img));
            
        //DEFINE OS PAR�METROS DA MINIATURA
        $tamanhoX = $largura;
        $tamanhoY = $altura; 
                
        //CRIA UMA NOVA IMAGEM
        if($tipo == "gif"){
            $imagem_orig = ImageCreateFromGIF($imagem_gerada);
        }elseif($tipo == "png"){
            $imagem_orig = ImageCreateFromPNG($imagem_gerada);
        }else{
            $imagem_orig = ImageCreateFromJPEG($imagem_gerada); 
        }        

        //LARGURA
        $pontoX = ImagesX($imagem_orig);

        //ALTURA
        $pontoY = ImagesY($imagem_orig);        
        
        
        //verifica se a largura � maior que o tamanho $tamanhoX
        if($pontoX > $tamanhoX)
        {
            //conta pra achar o nova altura
            $conta1 = $pontoY * $tamanhoX;
            $conta2 = $conta1/$pontoX;
            $largura = $tamanhoX;
            $altura = round($conta2);
        }
        else
        {
            $conta2 = $pontoY;
            $largura = $pontoX;
            $altura = $pontoY;
        }
    
        //verifica se altura � igual ou menos que $tamanhoY
        if($conta2 > $tamanhoY)
        {
            //conta pra achar o nova largura
            $conta3 = $pontoX * $tamanhoY;
            $conta4 = $conta3/$pontoY;
            $largura = round($conta4);
            $altura = $tamanhoY;
        }      

        //CRIA O THUMBNAIL
        $imagem_fin = ImageCreateTrueColor($largura, $altura);    

        //COPIA A IMAGEM ORIGINAL PARA DENTRO
        ImageCopyResampled($imagem_fin, $imagem_orig, 0, 0, 0, 0, $largura+1, $altura+1, $pontoX, $pontoY);
    
        //SALVA A IMAGEM
        if($tipo == "gif")
        {
            ImageGIF($imagem_fin, $imagem_gerada,100); 
        }
        elseif($tipo == "png")
        {

             imagealphablending($imagem_fin, false);
            imagesavealpha($imagem_fin,true);
            $transparent = imagecolorallocatealpha($imagem_fin, 255, 255, 255, 127);
            imagefilledrectangle($imagem_fin, 0, 0, $largura, $altura, $transparent); 

            //COPIA A IMAGEM ORIGINAL PARA DENTRO
            ImageCopyResampled($imagem_fin, $imagem_orig, 0, 0, 0, 0, $largura+1, $altura+1, $pontoX, $pontoY);            
            
            ImagePNG($imagem_fin, $imagem_gerada); 
        
        }
        else
        {
            ImageJPEG($imagem_fin, $imagem_gerada,100); 
        }
        
        //LIBERA A MEM�RIA
        ImageDestroy($imagem_orig);
        ImageDestroy($imagem_fin);
        
        // $this->largura = $largura;
        // $this->altura = $altura;	

    }
    
}