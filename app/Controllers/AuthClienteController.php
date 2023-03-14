<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\UserPermission;
use Respect\Validation\Validator as v;

class AuthClienteController extends Controller
{    
    public function login($request, $response)
    {
        if($request->isGet())
        {
            return $this->container->view->render($response, 'site/area-cliente-login.twig');
        }
         

        if(!$this->container->authUser->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        ))
        {
            return $response->withRedirect($this->container->router->pathFor('areacliente_login'));
        }

        return $response->withRedirect($this->container->router->pathFor('areacliente'));
    }

    public function register($request, $response)
    {

        if($request->isGet())
            return $this->container->view->render($response, 'areacliente_login');

        $validation = $this->container->validator->validate($request, [
            'nome' => v::notEmpty()->alpha()->length(10),
            'email' => v::notEmpty()->noWhitespace()->email(),
            'senha' => v::notEmpty()->noWhitespace()
        ]);

        if($validation->failed())
        {
            return $response->withRedirect($this->container->router->pathfor('areacliente_login'));
        } 

        $item = Cliente::where('email',$request->getParam('email'))->first();

        if($item)
        {
            $this->container->flash->addMessage('error','E-mail já cadastrado');
            return $response->withRedirect($this->container->router->pathfor('areacliente_login'));
        }
        
        $user = Cliente::create([
            'nome' => $request->getParam('nome'),
            'email' => $request->getParam('email'),
            'telefone' => $request->getParam('telefone'),
            'cpf' => $request->getParam('cpf'),
            'status' => 'A',
            'senha' => $request->getParam('senha'),
            'senhamd5' => password_hash($request->getParam('senha'), PASSWORD_DEFAULT),
        ]);

        // $user->permissions()->create(UserPermission::$defaults);

        // $payload = [
        //     'name' => $user->name,
        //     'email' => $user->email,
        //     'confirmation' => $key
        // ];

        //$this->container->mail->send($payload,'welcome.twig','Bem vindo ao sistema',$payload);


        $_SESSION['userCliente'] = $user->id;

        return $response->withRedirect($this->container->router->pathfor('areacliente'));
    }

    public function logout($request, $response)
    {
        if(isset($_SESSION['userCliente']))
        {
            unset($_SESSION['userCliente']);      
            unset($_SESSION['userClienteDados']);      
        }
        return $response->withRedirect($this->container->router->pathfor('areacliente_login'));
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