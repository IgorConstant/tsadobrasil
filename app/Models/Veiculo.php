<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    protected $table = 'app_veiculos';

    protected $fillable = [
                            'montadora_id',
                            'veiculo',
                            'veiculo_url',
                            'status',
                        ];

    public function montadora()
    {
        return $this->belongsTo(Montadora::class);
    }

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }

    public function produtos()
    {
        return $this->hasMany(ProdutoVeiculo::class);
    }

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

