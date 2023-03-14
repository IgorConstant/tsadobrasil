<?php

namespace App\Auth;

use App\Models\Cliente;

class AuthUser
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function user()
    {   
        if(isset($_SESSION['userCliente']))
        return Cliente::find($_SESSION['userCliente']);
    }

    public function checkUser()
    {
        return isset($_SESSION['userCliente']);
    }

    public function attempt(string $user, string $password)
    {
        $user = Cliente::where([['email', $user],['status','=','A']])->first();

        if(!$user || !password_verify($password, $user->senhamd5))
        {
            $this->container->flash->addMessage('error','Usuário ou senha inválidos! Tente novamente.');
            return false;
        }

        $_SESSION['userCliente'] = array(
                                        'id' => $user->id,
                                        'nome' => $user->nome,
                                        'tipo' => $user->tipo,
                                        'cpf' => preg_replace('/[^0-9]/', '', $user->cpf),
                                        'cnpj' => preg_replace('/[^0-9]/', '', $user->cnpj),
                                        'boleto_codigo' => $user->boleto_codigo,
                                        );

        return true;
    }
    
}