<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoFoto extends Model
{
    protected $table = 'app_produtos_imagens';

    protected $fillable = [
                        'produto_id',
                        'imagem',
                        'ordem',
                        ];

    public function produto()
    {
            return $this->belongsTo(Produto::class);
    }

}

