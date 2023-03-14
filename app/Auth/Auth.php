<?php

namespace App\Auth;

use App\Models\User;

class Auth
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function user()
    {   
        if(isset($_SESSION['user']))
        return User::find($_SESSION['user']);
    }

    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function attempt(string $user, string $password)
    {
        $user = User::where([['user', $user],['status','=','A']])->first();

        if(!$user || !password_verify($password, $user->senhamd5))
        {
            $this->container->flash->addMessage('error','Usuário ou senha inválidos! Tente novamente.');
            return false;
        }

        // if (!$user->is_confirmation) {
        //     $this->container->flash->addMessage(
        //         'error',
        //         "Você precisa confirmar seu cadastro antes de continuar."
        //     );
            
        //     return false;
        // }

        $_SESSION['user'] = $user->id;

        return true;
    }
    
}