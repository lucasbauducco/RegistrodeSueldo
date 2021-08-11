<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LegajoType;
use App\Form\ReciboType;
use App\Entity\User;
use App\Entity\Recibo;
use ZipArchive;
use App\encriptado;
use App\Form\BusquedaUserType;
use App\Entity\UserBusqueda;

class LegajoController extends AbstractController
{
    /**
     * @Route("/user/legajo", name="legajo")
     */
    public function legajo(Request $request)
    {
        $user = $this->getUser();

        $formulario = $this->createForm(LegajoType::class, $user);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('correcto', 'Se agregó correctamente');

            //Ver para dónde redirigir
            return $this->redirectToRoute('miLegajo');
        } else {

            return $this->render('legajo/agregarLegajo.html.twig', [
                'formulario' => $formulario->createView()
            ]);
        }
    }

    /**
     * @Route("/admin/usuariosLegajos", name="usuariosLegajos")
     */
    public function usuariosLegajos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(BusquedaUserType::class,new UserBusqueda());
        $form->handleRequest($request);
        $busqueda=$form->getData();


        $usuarios= $em->getRepository(User::class)->findAll();

        if ($form->isSubmitted()){
            return $this->render('usuarios/usuariosLegajos.html.twig', [
            'usuarios' => $this->buscarUsuarios($busqueda),'formulario' => $form->createView()
        ]);
        }
        else{
            return $this->render('usuarios/usuariosLegajos.html.twig', [
                'usuarios' => $this->buscarUsuariosAll(),'formulario' => $form->createView()
            ]);
        }
    }

    //------------------ BUSQUEDAS A LA BD A PATA --------------------------

    public function buscarUsuarios(UserBusqueda $busqueda){
        
        $manager=$this->getDoctrine()->getManager();
        
        $query = $manager->createQuery(
        "SELECT u
        FROM App\Entity\User u
        WHERE u.email LIKE :email
        AND u.legajo != ''
        ORDER BY u.id DESC
        "
        )->setParameter('email', '%' . $busqueda->getBuscar().'%');
        
        //Límite de resultados..
        $query->setMaxResults(100);
        
        //Retorna busqueda de la compra..
        return $query->getResult();
    }

    public function buscarUsuariosAll(){
        
        $manager=$this->getDoctrine()->getManager();
        
        $query = $manager->createQuery(
        "SELECT u
        FROM App\Entity\User u
        WHERE u.legajo != ''
        ORDER BY u.id DESC
        "
        );
        
        //Límite de resultados..
        $query->setMaxResults(300);
        
        //Retorna busqueda de la compra..
        return $query->getResult();
    }


    /**
     * @Route("/superadmin/legajos", name="mostrarLegajos")
     */
    public function mostrarLegajos(Request $request)
    {
        $recibo = new Recibo();
        $encriptado = new encriptado();

        $formulario = $this->createForm(ReciboType::class, $recibo);
        $formulario->handleRequest($request);
        $archivo = $formulario->get('path')->getData();
        
        if ($formulario->isSubmitted() && $formulario->isValid() && $this->validacionRecibo($recibo,$archivo)) {

            // $entityManager = $this->getDoctrine()->getManager();

            // $entityManager->persist($recibo);
            // $entityManager->flush();

            // Creo la dirección junto con el año.

            $direccion = "./uploads/recibos/" . $recibo->getAnio() ."/";

            // Me fijo si existe el archivo, sino lo creo.

            if (!is_dir($direccion)) {

                mkdir($direccion, 0777, false);

            }
            
            $extensionArchivo=$archivo->guessExtension();

            $nombreArchivo= time().".".$extensionArchivo;
            $nombreArchivo = $encriptado -> encriptar($nombreArchivo);



            $archivo->move("uploads/recibos/". $recibo->getAnio() . "/" ,$nombreArchivo);   

            $zip = new ZipArchive();

            if ($zip->open($direccion . $nombreArchivo)){

                $ver = $zip->extractTo($direccion . $recibo->getMes() . "/");
                $zip->close();
                //rename($direccion, $direccion . $recibo->getMes());
                $this->addFlash('correcto', 'Se agregó correctamente!');

                unlink($direccion . $nombreArchivo);

            }else{

                $this->addFlash('error', 'Hubo un error al descomprimir el archivo.');

            }
            
            return $this->redirectToRoute('mostrarLegajos');
        }
        
        return $this->render('legajo/mostrarLegajos.html.twig', [
            'folders' => $this->getFolders(),
            'formulario' => $formulario->createView()
        ]);
    }

    public function getFolders(){

        $user = $this->getUser();

        $direccion = "./uploads/recibos/";
        
        $dirArchivos = "";

        $archivos = [];
        
        $folders = [];
        $años = [];

        for ($i = 2015; $i < 2030; $i++) {

            $dirArchivos = $direccion . $i . "/";

            if (is_dir($dirArchivos)) {

                array_push($folders,$i);
                
                for ($j = 1; $j <= 14; $j++) {

                    $mes = $this->getMes($j);

                    if (is_dir($dirArchivos . $mes)) {

                        array_push($folders,$mes);
                        $archivos = array_diff(scandir($dirArchivos . $mes), array('..', '.'));
                        
                        if ($archivos != null && $archivos != ""){

                            $this -> encriptarPdf($archivos);
                            array_push($folders,$archivos);

                        }
                    }                    
                }
            }
        }

        return $folders;
    }

    public function getFoldersMiLegajo(){

        $user = $this->getUser();

        $direccion = "./uploads/recibos/";
        
        $dirArchivos = "";

        $archivos = [];
        
        $folders = [];
        $años = [];

        for ($i = 2015; $i < 2040; $i++) {

            $dirArchivos = $direccion . $i . "/";

            if (is_dir($dirArchivos)) {

                array_push($folders,$i);
                
                for ($j = 1; $j <= 14; $j++) {

                    $mes = $this->getMes($j);

                    if (is_dir($dirArchivos . $mes)) {

                        array_push($folders,$mes);
                        
                        $archivos = array_diff(scandir($dirArchivos . $mes), array('..', '.'));

                        if ($archivos != null && $archivos != ""){

                            array_push($folders,$archivos);

                        }

                    }                    
                }
            }
        }

        return $folders;
    }

    public function getMes($numero){
        switch ($numero) {
            case 1:
                return "Enero";
                break;
            case 2:
                return "Febrero";
                break;
            case 3:
                return "Marzo";
                break;
            case 4:
                return "Abril";
                break;
            case 5:
                return "Mayo";
                break;
            case 6:
                return "Junio";
                break;
            case 7:
                return "Julio";
                break;
            case 8:
                return "Agosto";
                break;
            case 9:
                return "Septiembre";
                break;
            case 10:
                return "Octubre";
                break;
            case 11:
                return "Noviembre";
                break;
            case 12:
                return "Diciembre";
                break;
            case 13:
                return "SAC Junio";
                break;
            case 14:
                return "SAC Diciembre";
                break;
            
        }
    }

    public function validacionRecibo($recibo,$archivo){

        //Hacer validaciones.

        if($archivo == null){
            $this->addFlash('error', 'Debe seleccionar un archivo.');
            return false;
        }
        $extensionArchivo=$archivo->guessExtension();
        
        if($recibo -> getMes() == 'Imposible'){
            $this->addFlash('error', 'Debe seleccionar un mes.');
            return false;
        }
        else if ($recibo -> getAnio() == 0){
            $this->addFlash('error', 'Debe seleccionar un año.');

            return false;
        }
        else if ($extensionArchivo != 'zip'){
            $this->addFlash('error', 'Sólo se admiten archivos .zip');
            return false;
        }

        // Del mes. Chequear el form ReciboType

        // Que el archivo sea sólo zip.

        return true;
    }

    /**
     * @Route("/user/miLegajo", name="miLegajo")
     */
    public function miLegajo(Request $request)
    {
        return $this->render('legajo/miLegajo.html.twig', [
            'folders' => $this->getFoldersMiLegajo()
        ]);
    }


    public function encriptarPdf($archivos){

        $encriptado =  new encriptado();
        $stringEncriptado = "";

        foreach($archivos as $pdf){
            $stringEncriptado = $encriptado -> encriptar($pdf);
        }
        return $stringEncriptado;
    }
}