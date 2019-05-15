<?php

namespace MadridEnPie\DonacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Swift_Attachment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ps\PdfBundle\Annotation\Pdf;
use MadridEnPie\DonacionesBundle\Entity\Donacion;
use MadridEnPie\DonacionesBundle\Form\DonacionType;

class DefaultController extends Controller{
    
	/**
     * @Route("/", name="donaciones_inicio")
     */
	public function indexAction(){
		//Primero se buscan campa�as activas
		$repositoryCampanias = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:CampaniaDonaciones');
		$campania = $repositoryCampanias->find(1);
		
		//Se buscan los cr�ditos recibidos
		$repository = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:Donacion');
		$qbTotalRecibidos = $repository->createQueryBuilder('c')
		->select('SUM(c.importe)')
		->where('c.recibido = 1');
		
		$totalRecibidos = $qbTotalRecibidos->getQuery()->getSingleScalarResult();
		
        return $this->render('MadridEnPieDonacionesBundle:Default:index.html.twig', array('totalRecibidos' => $totalRecibidos, 'campania' => $campania));
    }
	
	/**
     * @Route("/formulario", name="donaciones_formulario")
	 * @Pdf()
     */
	 public function formulario(Request $request){
	 	//Primero se buscan campa�as activas
	 	$repositoryCampanias = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:CampaniaDonaciones');
	 	$campania = $repositoryCampanias->find(1);
	 	//$totalCampaniasActivas = $qbCampaniasActivas->getQuery()->getSingleScalarResult();
	 	
	 	//Si no hay campa�as activas, no se muestra el formulario
	 	if(!$campania->getActiva()){
	 		return $this->render('MadridEnPieDonacionesBundle:Default:no_campania.html.twig');
	 	}
	 	
		//Se carga el formulario
		$donacion = new Donacion();
		$form = $this->createForm(new DonacionType(), $donacion, array('csrf_protection' => false));
		$form->handleRequest($request);
		
		//Se buscan los cr�ditos recibidos
		$repository = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:Donacion');
		$qbTotalRecibidos = $repository->createQueryBuilder('c')
							->select('SUM(c.importe)')
							->where('c.recibido = 1');
		
		$totalRecibidos = $qbTotalRecibidos->getQuery()->getSingleScalarResult();
		
		//Si la validación es correcta, se persiste el crédito y se redirige a mostrar el contrato
		if ($form->isValid()) {
			$donacion->setIdentificador("Temp");
			
			//Se guarda el crédito
			$em = $this->getDoctrine()->getManager();
			$em->persist($donacion);
			$em->flush();
			
			//Se crea el identificador de verdad
			$identificador = 'DonaMadridEnPie'. $donacion->getId() . self::stringAleatorio();
			//Se actualiza el identificador
			//$em = $this->getDoctrine()->getManager();
			$donacion->setIdentificador($identificador);
			$em->flush();
			
			//Se crea el pdf
			$facade = $this->get('ps_pdf.facade');
			$response = new Response();
			$this->render('MadridEnPieDonacionesBundle:Default:contrato.pdf.twig', array('donacion' => $donacion), $response);
			
			$xml = $response->getContent();
			$content = $facade->render($xml);
			
			//Se escribe a disco el pdf
			$ruta = $this->get('kernel')->locateResource('@MadridEnPieDonacionesBundle/Resources/contratos/');
			$fs = new Filesystem();
			$fs->dumpFile($ruta . $identificador .'.pdf', $content);
			
			//Se manda el correo
			$mailer = $this->get('mailer');
			$message = $mailer->createMessage()
				->setSubject('Contrato de donación con Madrid en Pie')
				->setFrom('DonacionesIUMEPM@gmail.com')
				->setTo($donacion->getCorreoElectronico())
				->setBody(
					$this->renderView(
						'MadridEnPieDonacionesBundle:Default:correo.txt.twig',
						array('donacion' => $donacion)
					),
					'text/plain'
				)
				->attach(Swift_Attachment::fromPath($ruta . $identificador .'.pdf'))
			;
			$mailer->send($message);
			
			return $this->redirectToRoute('donaciones_contrato', array('identificador' => $donacion->getIdentificador()));
		}
		
		//Si no se ha enviado el formulario, se carga la página con el formulario
		return $this->render('MadridEnPieDonacionesBundle:Default:formulario.html.twig', array(
                    'form' => $form->createView(),
					'totalRecibidos' => $totalRecibidos,
					'campania' => $campania
		));
	 }
	 
	 /**
     * @Route("/contrato/{identificador}", name="donaciones_contrato")
     */
	 public function contrato($identificador){
		//Se busca el crédito por su identificador
		$repository = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:Donacion');
		$donacion = $repository->findOneByIdentificador($identificador);
		//Si no se ha encontrado, no se puede crear el pdf. Se muestra la página de eror
		if(!$donacion){
			return $this->render('MadridEnPieDonacionesBundle:Default:error_contrato.html.twig', array('identificador' => $identificador));
		}
		
        return $this->render('MadridEnPieDonacionesBundle:Default:contrato_ok.html.twig', array('donacion' => $donacion));
		//return new Response($content, 200, array('content-type' => 'application/pdf'));
	 }
	 
	 /**
     * @Route("/pdf/{identificador}", name="donaciones_pdf")
	 * @Pdf()
     */
	 public function pdf($identificador){
		//Se busca el crédito por su identificador
		$repository = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:Donacion');
		$donacion = $repository->findOneByIdentificador($identificador);
		//Si no se ha encontrado, no se puede crear el pdf. Se muestra la página de eror
		if(!$donacion){
			return $this->render('MadridEnPieDonacionesBundle:Default:error_contrato.html.twig', array('identificador' => $identificador));
		}
		
		//Se guarda el crédito
		$em = $this->getDoctrine()->getManager();
		$em->persist($donacion);
		$em->flush();
		
		//Se crea el pdf
		$facade = $this->get('ps_pdf.facade');
		$response = new Response();
		$this->render('MadridEnPieDonacionesBundle:Default:contrato.pdf.twig', array('donacion' => $donacion), $response);
		
		$xml = $response->getContent();
		$content = $facade->render($xml);
		
		return new Response($content, 200, array('content-type' => 'application/pdf'));
	 }
	 
	 private function stringAleatorio($length = 3) {
		//$char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$char = str_shuffle($char);
		for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
			$rand .= $char{mt_rand(0, $l)};
		}
		return $rand;
	}
}
