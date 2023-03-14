<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
        protected $table = 'app_produtos';

        protected $fillable = [
                                'linha_id',
                                'montadora_id',
                                'codigo_tsa',
                                'codigo_tsa_url',
                                'codigo_original',
                                'codigo_outros',
                                'codigo_oculto',
                                'compativel',
                                'valor_ohmico',
                                'observacoes',
                                'relacionados',
                                'status',
                                'arquivo_manual',
                                'arquivo_lamina',
                                'keywords',
                                ];


        public function linha()
        {
                return $this->belongsTo(Linha::class);
        }

        public function montadora()
        {
                return $this->belongsTo(Montadora::class);
        }

        public function veiculo()
        {
                return $this->belongsTo(Veiculo::class);
        }

        public function modelo()
        {
                return $this->belongsTo(Modelo::class);
        }

        public function fotos()
        {
                return $this->hasMany(ProdutoFoto::class);
        }

        public function veiculos()
        {
                return $this->hasMany(ProdutoVeiculo::class)->with('veiculo')->with('modelo');
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

