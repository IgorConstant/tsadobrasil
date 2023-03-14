<?php

namespace App\Controllers;


class DashboardController extends Controller
{    
    public function index($request, $response)
    {
        
        return $this->container->view->render($response,'admin/dashboard.twig');
    }
}