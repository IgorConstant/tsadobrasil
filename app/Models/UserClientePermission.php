<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClientePermission extends Model
{
    protected $table = 'users_cliente_permissions';

    protected $fillable = [
        'is_admin'
    ];

    public static $defaults = [
        'is_admin' => false
    ];
}

