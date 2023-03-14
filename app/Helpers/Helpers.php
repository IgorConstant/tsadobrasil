<?php

namespace App\Helpers;

class Helpers
{
    
    public static function url($str)
    {   
        $before = array(
            'àáâãäåòóôõöøèéêëðçìíîïùúûüñšž',
            '/[^a-z0-9\s]/',
            array('/\s/', '/--+/', '/---+/')
        );
    
        $after = array(
            'aaaaaaooooooeeeeeciiiiuuuunsz',
            '',
            '-'
        );
    
        $str = strtolower($str);
        $str = strtr($str, $before[0], $after[0]);
        $str = preg_replace($before[1], $after[1], $str);
        $str = trim($str);
        $str = preg_replace($before[2], $after[2], $str);
    
        return $str;	
    }

    public static function youtube($youtube)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube, $match);
        return $match[1];
    }

    public static function grana($gn){	
        $gn2 = str_replace('.', '', $gn);
        $gn2 = str_replace(',', '.', $gn2);    
        return $gn2;
    }

    public static function dinheiro($dn){	
        $dn2 =  number_format($dn,2,',','.');
        return $dn2;
    }

    public static function cod()
    {	

        $caracteres='0123456789abcdef0123456789';
        $numch=32;
        $novaSenha='';
        for($i=0;$i<$numch;$i++) {$novaSenha.=$caracteres[rand(0,strlen($caracteres)-1)];
        }
    
        return $novaSenha;
    }

    //gera codigo
    public static function codSenha()
    {	

        $caracteres='0123456789';
        $numch=6;
        $novaSenha='';
        for($i=0;$i<$numch;$i++) {$novaSenha.=$caracteres[rand(0,strlen($caracteres)-1)];
        }

        return $novaSenha;
    }

    public static function data($dt,$v = null){
		
        if(!empty($dt)){
            if(!empty($v)){
                $parte = explode("-",$dt);
                $ver =  $parte[2]."/".$parte[1]."/".$parte[0];
            }else{
                $parte = explode("/",$dt);
                $ver =  $parte[2]."-".$parte[1]."-".$parte[0];
            }
        }else{
            $ver = "0000-00-00";
        }
        
        return $ver;
    }

    public static function getPaginate($config)
    {
        if($config['pages']>1)
        {
            $current = $config['current'];
            $pages = '<nav class="pages"><ul class="pagination justify-content-center">';
            for($i=1;$i<=$config['pages'];$i++)
            {
                $currentPage = ($current==$i)?'page-item active':'page-link';
                $pages .= '<li><a href="?page='.$i.$config['links'].'" class="'.$currentPage.'">'.$i.'</a></li>';
            }
            $pages .= '</ul></nav>';
        }
        else
        {
            $pages = '';
        }

        return $pages;
       
    }

    public static function getPaginateSite($config)
    {
        if($config['pages']>1)
        {
            $current = $config['current'];
            $pages = '<ul class="uk-pagination" uk-margin>';
            for($i=1;$i<=$config['pages'];$i++)
            {
                $currentPage = ($current==$i)?'uk-active':'';
                $pages .= '<li class="'.$currentPage.'"><a href="'.$config['url'].'?page='.$i.$config['links'].'">'.$i.'</a></li>';
            }
            $pages .= '</ul>';
        }
        else
        {
            $pages = '';
        }

        return $pages;
       
    }

    public static function getFilters($config)
    {	       
        $gets = '';
        foreach($config as $key => $v)
        {
            if($v){
                $gets .= '&'.$key.'='.$v; 
            }
        }
        return $gets;       
    }

    function keywords($str) {
        $rules = array(".", "-", "_");
        $str = str_replace($rules, "", $str);
        return $str;
    }

    
}