<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representante extends Model
{
    protected $table = 'app_representantes';

    protected $fillable = [
                            'nome',
                            'nome_url',
                            'contato',
                            'documento',
                            'endereco',
                            'numero',
                            'bairro',
                            'cidade',
                            'estado',
                            'cep',
                            'referencia',
                            'telefone',
                            'whatsapp',
                            'email',
                            'googlemaps',
                        ];

    public function scopeFilter($query, $filtro, $filtro_campos)
	{
        $filtro = explode(" ",$filtro);
        return $query->where(function($q) use ($filtro, $filtro_campos) {
            foreach ($filtro_campos as $key => $campo){
                $q->orWhere(function($q) use ($filtro, $campo) {
                    foreach ($filtro as $key => $var){
                        $q->Where($campo,'like','%'.$var.'%');
                    }
                });
            }
        }); 
	}

    public function scopePaginates($query, $config)
	{
        return $query->paginate($config['limite'], ['*'], 'page', $config['page']); 
	}


}

