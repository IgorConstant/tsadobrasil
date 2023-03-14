<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoVeiculo extends Model
{
    protected $table = 'app_produtos_veiculos';

    protected $fillable = [
                            'produto_id',
                            'montadora_id',
                            'veiculo_id',
                            'modelo_id',
                            'motor',
                            'ano',
                            'ano_de',
                            'ano_ate',
                            'combustivel',
                            ];

    public function produto()
    {
            return $this->belongsTo(Produto::class);
    }

    public function modelo()
    {
            return $this->belongsTo(Modelo::class);
    }

    public function veiculo()
    {
            return $this->belongsTo(Veiculo::class);
    }

}

