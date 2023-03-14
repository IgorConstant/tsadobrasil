<?php

namespace App\Middleware;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(!$this->container->auth->check())
        {
            return $response->withRedirect($this->container->router->pathFor('app.login'));
        }
        
        $response = $next($request, $response);
        return $response;
    }
}