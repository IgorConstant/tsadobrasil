<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{    
    public function login($request, $response)
    {
        if($request->isGet())
        {
            return $this->container->view->render($response, 'admin/login.twig');
        }
            

        if(!$this->container->auth->attempt(
            $request->getParam('user'),
            $request->getParam('password')
        ))
        {
            return $response->withRedirect($this->container->router->pathFor('app.login'));
        }

        return $response->withRedirect($this->container->router->pathFor('app.home'));
    }

    public function register($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'register.twig');

        $validation = $this->container->validator->validate($request, [
            'name' => v::notEmpty()->alpha()->length(10),
            'email' => v::notEmpty()->noWhitespace()->email(),
            'password' => v::notEmpty()->noWhitespace()
        ]);

        if($validation->failed())
        {
            return $response->withRedirect($this->container->router->pathfor('auth.register'));
        }
        
        $now = new \DateTime( date('m/d/Y H:i:s'));
        $now->modify('+1 hour');

        $key = bin2hex(random_bytes(20));

        $user = User::create([
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            'confirmation_key' => $key,
            'confirmation_expires' => $now
        ]);

        $user->permissions()->create(UserPermission::$defaults);

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'confirmation' => $key
        ];

        //$this->container->mail->send($payload,'welcome.twig','Bem vindo ao sistema',$payload);

        return $response->withRedirect($this->container->router->pathfor('app.login'));
    }

    public function logout($request, $response)
    {
        if(isset($_SESSION['user']))
        {
            unset($_SESSION['user']);            
        }
        return $response->withRedirect($this->container->router->pathfor('app.login'));
    }

     public function confirmation($request, $response)
    {
        $user = User::where('confirmation_key', $request->getParam('confirmation'))->first();

        if(!$user)
            $this->container->flash->addMessage('error', 'A conta que você está tentando confirmar não existe.');

        if (strtotime(date('d/m/Y H:i:s')) > strtotime($user->confirmation_expires)) {
            $this->container->flash->addMessage('error', "Parece que você demorou um pouco para 
                confirmar o e-mail em? Não tem problema,
                clique <a href='". $this->container->router->pathFor('auth.resend')."?email=".$user->email."'>aqui</a> para reenviar.");
        } else {
            $this->container->flash->addMessage('success', 'Conta confirmada com sucesso!');
            $user->is_confirmation = true;
            $user->save();
        }

        return $response->withRedirect($this->container->router->pathFor('auth.login'));
    }

    public function resend($request, $response)
    {
        if(empty($request->getParam('email'))) {
            $this->container->flash->addMessage('error', 'Houve um erro ao tentar processar a sua solicitação.');
            return $response->withRedirect($this->container->router->pathFor('auth.login'));
        }

        $now = new \Datetime(date('d/m/Y H:i:s'));
        $now->modify('+1 hour');
        $key = bin2hex(random_bytes(20));

        $user = User::where('email', $request->getParam('email'))->first();
        $user->confirmation_key = $key;
        $user->confirmation_expires = $now;
        $user->save();

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'confirmation' => $key,
        ];

        $this->container->mail->send($payload, 'resend.twig', 'Reenvio de confirmação!', $payload);

        $this->container->flash->addMessage('success', 'Um novo e-mail de confirmação foi enviado com sucesso!');
        return $response->withRedirect($this->container->router->pathFor('auth.login'));
    }

}