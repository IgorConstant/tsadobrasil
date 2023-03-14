<?php

namespace App\Middleware;

class AuthUserMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(!$this->container->authUser->checkUser())
        {
            return $response->withRedirect($this->container->router->pathFor('areacliente_login'));
        }
        
        $response = $next($request, $response);
        return $response;
    }
}