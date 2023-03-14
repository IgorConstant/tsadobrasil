<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'app_clientes';

    protected $fillable = [
                        'nome',
                        'email',
                        'tipo',
                        'cpf',
                        'cnpj',
                        'boleto',
                        'boleto_codigo',
                        'senha',
                        'senhamd5',
                        'status',
                        ];

    // public function permissions()
    // {
    //     return $this->hasOne('App\Models\UserClientePermission','user_id');
    // }

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

