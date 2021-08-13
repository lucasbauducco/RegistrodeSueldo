<?php

namespace App\Controller;

use App\Entity\Recibo;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Validator\Constraints\DateTime;

class ReciboController extends AbstractController
{
    /**
     * @Route("/recibo/{id}", name="app_recibo_id")
     */
    public function index($id)
    {
        //$comprobante= $pago->getComprobantePago();
       // return $this->redirect("../ComprobantedePago/".$comprobante);
    }
    /**
     * @Route("/load", name="load")
     */
    public function load()
    {
        set_time_limit(8100);
        $manager= $this->getDoctrine()->getManager();
        //traer todos los usuarios
        $user= $manager->getRepository(User::class)->findAll();
        $array = array(
            1=> "Enero",
            2=> "Febrero",
            3=> "Marzo",
            4=> "Abril",
            5=> "Mayo",
            6=> "Junio",
            7=> "Julio",
            8=> "Agosto",
            9=> "Septiembre",
            10=> "Octubre",
            11=> "Noviembre",
            12=> "Diciembre",
            13=> "SAC Junio",
            14=> "SAC Diciembre"
        );
        
        foreach ($user as $usuarios) {
            $legajo= $usuarios->getLegajo();
            $email= $usuarios-> getEmail();
            $id= $usuarios-> getId();
            $i=2018;
            $fechaCarga= new \DateTime();
            $anioActual= date("Y");
            //recorro las carpetas por año, mes
            //if(!$legajo=null){
                while ($i <= 2021 && $legajo!=null ) {
                    foreach ($array as $mes) {
                        $uploads_dir = '..\\uploads\\recibos\\'."".$i.'\\'."".$mes;
                        
                        $configDirectories = array($uploads_dir);
                        //pregunto si existe el archivo en la direccion
                        $pathArchive='..\\uploads\\recibos\\'."".$i.'\\'."".$mes.'\\'."".$legajo.".pdf";
                        
                        if (file_exists('..\\uploads\\recibos\\'."".$i.'\\'."".$mes.'\\'."".$legajo.".pdf")) {
                            //guardo recibos en la tabla
                            
                            $anio=$i;
                            $estado="Activo";
                            $recibo= new Recibo($legajo, $email, $fechaCarga, $pathArchive, $anio,$mes, $estado);
                            
                            $manager->persist($recibo);
                            $manager->flush();
                        } else {
                        ////////no hace nada
                        }
                
                    }
                    $i++;
                }
        //  }
            
        }
        return $this->redirectToRoute("redirectInicio");

    }

    
}
