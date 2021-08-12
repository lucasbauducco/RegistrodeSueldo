<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class InicioController extends AbstractController
{
    /**
     * @Route("/", name="redirectInicio")
     */
    public function redirectInicio(Request $request)
    {

        
        return $this->redirectToRoute("inicio");
    }
    /**
     * @Route("/inicio", name="inicio")
     */
    public function index()
    {
        return $this->render('inicio/inicio.html.twig');
    }
}
