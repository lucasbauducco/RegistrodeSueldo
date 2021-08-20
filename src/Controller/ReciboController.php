<?php

namespace App\Controller;

use App\Entity\Recibo;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use App\Form\CargarRecibosType;
class ReciboController extends AbstractController
{
    /**
     * Carga una lista de recibos a la tabla recibos de la bd y los guarda en una carpeta uploads
     * 
     * conficurar php.ini server
     * 
     * max_file_uploads = 600
     * 
     * 
     * @Route("/cargarRecibo", name="app_cargar_recibo")
     */
    public function cargarRecibo(Request $request,SluggerInterface $slugger){
        $manager= $this->getDoctrine()->getManager();
        $dateTime = new \DateTime();
        //$day = $dateTime->format('d-m-Y');

        $manager=$this->getDoctrine()->getManager();
        
        $formulario = $this->createForm(CargarRecibosType::class);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            
                //traigo los archivos cargados del formulario y los almaceno en una variable
                $archivos = $formulario->get('archivos')->getData();
                $mes=$formulario->get('mes')->getData();
                $anio=$formulario->get('fecha')->getData();
                $anio=  $anio->format("Y");
                
                try {
                    //esta funcion guarda los archivos en una carpeta y en la tabla recibo
                    $cargando= $this->guardarDatos($slugger,$archivos,$manager,$anio,$mes,$dateTime,$formulario);


                    if($cargando){
                        $this -> addFlash('succes', '¡Los archivos se cargaron exitosamente!');
                        return $this->render('recibo/cargarRecibos.html.twig', [
                            'formulario' => $formulario->createView(),
                        ]);
                    }else{
                        $this -> addFlash('error', '¡Error en la carga de archivos!');
                        return $this->render('recibo/cargarRecibos.html.twig', [
                            'formulario' => $formulario->createView(),
                        ]);
                    }

                }catch (\Throwable $th) {
                    $this -> addFlash('error', '¡Error al guardar los archivos!'.$th);
                    return $this->render('recibo/cargarRecibos.html.twig', [
                        'formulario' => $formulario->createView(),
                    ]);
                }
        }
        return $this->render('recibo/cargarRecibos.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }

   
    private function guardarDatos($slugger,$archivos, $manager,$anio,$mes,$dateTime,$formulario){
        
            try {
                
                foreach ($archivos as $archivo){
                    /** @var UploadedFile $brochureFile */
                    //extraigo el nombre del archivo
                    $originalFilename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                    $usuario= new User();
                    //busco el usuario por el legajo cargado en el nombre del archivo
                    $usuario= $manager->getRepository(User::class)->findOneBy(array('legajo' =>  $originalFilename));
                    //$brochureFile->getClientOriginalName()
                            // this is needed to safely include the file name as part of the URL
                            //creo la dirreccion url del archivo
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'.'.$archivo->guessExtension();
                            $newFilename= '\\Intranet\\uploads\\recibos\\'."".$anio.'\\'."".$mes.'\\'.$newFilename;
                            //si tienen correo se les asigna sino se les sea no asignado
                    if($usuario!=null)
                    {
                            //genero un objeto recibo y seteo los parametros por constructor
                            $documento= new Recibo($originalFilename,$usuario->getEmail(),$dateTime, $newFilename, $anio,$mes,"Activo");
                            //almaceno el archivo en la carpeta
                            $uploads_dir = '..\\uploads\\recibos\\'."".$anio.'\\'."".$mes;
                            
                            $archivo->move(
                                $uploads_dir,
                                $newFilename
                            );
                            //guardo en base de datos
                            $manager->persist($documento);
                            $manager->flush();
                        
                    }else {
                        $usuario= null;
                        $email= "no tiene";
                        //genero un objeto recibo y seteo los parametros por constructor
        
                        $documento= new Recibo($originalFilename,$email,$dateTime, $newFilename, $anio,$mes,"Activo");
                        //almaceno el archivo en la carpeta
                        $uploads_dir = '..\\uploads\\recibos\\'."".$anio.'\\'."".$mes;
                        
                        $archivo->move(
                            $uploads_dir,
                            $newFilename
                        );
                        //guardo en base de datos
                        $manager->persist($documento);
                        $manager->flush();
                        
                    }
                }
                return true;
            } catch (\Throwable $th) {
                
                return false;
            }             
    }
    
  



    /**
     * 
     * @Route("/recibo", name="app_recibo")
     */
    public function recibo()
    {
        $manager= $this->getDoctrine()->getManager();
        //traer todos los usuarios
        $recibos= $manager->getRepository(Recibo::class)->findAll();

        return $this->render('recibo/dataTable.html.twig', ["recibos"=> $recibos]);
    }
    /**
     * 
     * @Route("/recibo/{id}", name="app_recibo_id")
     */
    public function reciboId($id)
    {
        $manager= $this->getDoctrine()->getManager();
        //traer todos los usuarios
        $recibos= $manager->getRepository(Recibo::class)->findOneBy(array('legajo' => $id));
        if(!$recibos!=null){
            $this -> addFlash('success', 'Ya puede visualizar su Recibo de Sueldo!');
            return $this->render('recibo/listarRecibos.html.twig', ["recibos"=> $recibos]);
            
        }
        $this -> addFlash('error', '(id)Error no pudimos cargar sus Recibos de Sueldo, si el error persiste consultar a soporte@unraf.com.ar');
        return $this->render('recibo/listarRecibos.html.twig', ["recibos"=> $recibos]);
    }
    /**
     * 
     * @Route("/reciboAnio/{anio}", name="app_recibo_Anio")
     */
    public function reciboAnio($anio)
    {
        $manager= $this->getDoctrine()->getManager();
        //traer todos los usuarios
        $recibos= $manager->getRepository(Recibo::class)->findOneBy(array('anio' => $anio));
        if(!$recibos!=null){
            $this -> addFlash('success', 'Ya puede visualizar su Recibo de Sueldo!');
            return $this->render('recibo/listarRecibos.html.twig', ["recibos"=> $recibos]);
            
        }
        $this -> addFlash('error', '(Anio)Error no pudimos cargar sus Recibos de Sueldo, si el error persiste consultar a soporte@unraf.com.ar');
        return $this->render('recibo/listarRecibos.html.twig', ["recibos"=> $recibos]);
    }
    /**
     * 
     * @Route("/reciboMes/{mes}", name="app_recibo_mes")
     */
    public function reciboMes($mes)
    {
        $manager= $this->getDoctrine()->getManager();
        //traer todos los usuarios
        $recibos= $manager->getRepository(Recibo::class)->findOneBy(array('anio' => $mes));
        if(!$recibos!=null){
            $this -> addFlash('success', 'Ya puede visualizar su Recibo de Sueldo!');
            return $this->render('recibo/listarRecibos.html.twig', ["recibos"=> $recibos]);
            
        }
        $this -> addFlash('error', '(mes)Error no pudimos cargar sus Recibos de Sueldo, si el error persiste consultar a soporte@unraf.com.ar');
        return $this->render('recibo/listarRecibos.html.twig', ["recibos"=> $recibos]);
    }
    /**
     * 
     * @Route("/reciboUsuario/{usuario}", name="app_recibo_usuario")
     */
    public function reciboUsuario($usuario)
    {
        $manager= $this->getDoctrine()->getManager();
        //traer todos los usuarios
        $recibos= $manager->getRepository(Recibo::class)->findBy(array('usuario' => $usuario));
        
        //$this -> addFlash('error', '(mes)Error no pudimos cargar sus Recibos de Sueldo, si el error persiste consultar a soporte@unraf.com.ar');
        return $this->render('recibo/listarRecibos.html.twig', ["recibos"=> $recibos]);
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
                        $pathArchive='\\Intranet\\uploads\\recibos\\'."".$i.'\\'."".$mes.'\\'."".$legajo.".pdf";
                        
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
