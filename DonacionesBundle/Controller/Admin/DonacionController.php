<?php

namespace MadridEnPie\DonacionesBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Attachment;
use MadridEnPie\AdminBaseBundle\Controller\AdminController as AdminController;
use MadridEnPie\DonacionesBundle\Entity\Donacion;

class DonacionController extends AdminController{
	
	/**
	 * @Route("/apladmin/listar-donaciones", name="listar_donaciones")
	 */
	public function listarDonaciones(Request $request){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN, parent::ROL_CONSULTA));
		if($response != null) return $response;
		
		//Se crea y se recoge el formulario
		$form = $this->createFormBuilder()
			->setMethod('GET')
	        ->add('identificador', 'text')
	        ->add('nombre', 'text')
	        ->add('apellidos', 'text')
	        ->add('documentoIdentidad', 'text')
	        ->add('Buscar', 'submit')
	        ->getForm();
	
	    $form->handleRequest($request);
	    
	    $data = $form->getData();
	    
	    //Se recogen las campañas de microcr�ditos
	    $repoMicro = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:CampaniaDonaciones');
	    $campanias = $repoMicro->findAll();
		
		//Se buscan los créditos
		$repository = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:Donacion');
		$qb = $repository->createQueryBuilder('c')
				->orderBy('c.id', 'DESC');
		
		//Se a�aden al queryBuilder las opciones del buscador, si las hay
		if($data['identificador'] != null && !empty(trim($data['identificador']))){
			$qb->andWhere('c.identificador LIKE :identificador')
            ->setParameter('identificador', '%'.$data['identificador'].'%');
		}
		
		if($data['nombre'] != null && !empty(trim($data['nombre']))){
			$qb->andWhere('c.nombre LIKE :nombre')
			->setParameter('nombre', '%'.$data['nombre'].'%');
		}
		
		if($data['apellidos'] != null && !empty(trim($data['apellidos']))){
			$qb->andWhere('c.apellidos LIKE :apellidos')
			->setParameter('apellidos', '%'.$data['apellidos'].'%');
		}
		
		if($data['documentoIdentidad'] != null && !empty(trim($data['documentoIdentidad']))){
			$qb->andWhere('c.documentoIdentidad LIKE :documentoIdentidad')
			->setParameter('documentoIdentidad', '%'.$data['documentoIdentidad'].'%');
		}
		
		$query = $qb->getQuery();
		
		$donaciones = $query->getResult();
		
		//Se buscan el total del importe de todos
		$qbTotal = $repository->createQueryBuilder('c')
							->select('SUM(c.importe)');
		
		$total = $qbTotal->getQuery()->getSingleScalarResult();
		
		//Se buscan los cr�ditos recibidos
		$qbRecibidos = $repository->createQueryBuilder('c')
							->select('COUNT(c.id)')
							->where('c.recibido = 1');
		
		$recibidos = $qbRecibidos->getQuery()->getSingleScalarResult();
		
		//Se buscan el total del importe de los recibidos
		$qbTotalRecibidos = $repository->createQueryBuilder('c')
		->select('SUM(c.importe)')
		->where('c.recibido = 1');
		
		$totalRecibidos = $qbTotalRecibidos->getQuery()->getSingleScalarResult();
		
		//Paginaci�n
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$donaciones, $this->get('request')->query->get('page', 1), 25
		);
	
		return $this->render('MadridEnPieDonacionesBundle:Admin:listar_donaciones.html.twig', array(
				'form' => $form->createView(),
				'pagination' => $pagination,
				'total' => $total,
				'recibidos' => $recibidos,
				'totalRecibidos' => $totalRecibidos, 
				'campanias' => $campanias
		));
	}
	
	/**
	 * @Route("/apladmin/detalle-donacion/{id}", name="detalle_donacion")
	 */
	public function detalleDonacion(Request $request, $id){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN, parent::ROL_CONSULTA));
		if($response != null) return $response;
		
		//Se buscan el cr�dito
		$repository = $this->getDoctrine()->getRepository('MadridEnPieDonacionesBundle:Donacion');
		$donacion = $repository->find($id);
		
		return $this->render('MadridEnPieDonacionesBundle:Admin:detalle_donacion.html.twig', array('donacion' => $donacion));
	}
	
	/**
	 * @Route("/apladmin/recibir-donacion/{id}/{recibir}", name="recibir_donacion")
	 */
	public function recibirDonacion(Request $request, $id, $recibir){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN));
		if($response != null) return $response;
	
		//Se buscan el cr�dito
		$em = $this->getDoctrine()->getManager();
		$donacion = $em->getRepository('MadridEnPieDonacionesBundle:Donacion')->find($id);
		$donacion->setRecibido($recibir);
		$em->flush();
		
		//Se env�a el correo al que ha prestado
		if($recibir == 1){
			$mailer = $this->get('mailer');
			$message = $mailer->createMessage()
			->setSubject('Madrid en Pie: Transferencia recibida')
			->setFrom('DonacionesIUMEPM@gmail.com')
			->setTo($donacion->getCorreoElectronico())
			->setBody(
					$this->renderView('MadridEnPieDonacionesBundle:Admin:correo_transferencia_recibida.txt.twig'),
					'text/plain'
			);
			$mailer->send($message);
		}
		
		//Se guarda el mensaje
		$sesion = $this->getRequest()->getSession();
		$sesion->getFlashBag()->add('mensaje', 'El crédito se ha cambiado de estado correctamente');
	
		return $this->redirectToRoute('listar_donaciones');
	}
	
	/**
	 * @Route("/apladmin/borrar-donacion/{id}", name="borrar_donacion")
	 */
	public function borrarDonacion(Request $request, $id){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN));
		if($response != null) return $response;
	
		//Se buscan el cr�dito
		$em = $this->getDoctrine()->getManager();
		$donacion = $em->getRepository('MadridEnPieDonacionesBundle:Donacion')->find($id);
		$em->remove($donacion);
		$em->flush();
	
		//Se guarda el mensaje
		$sesion = $this->getRequest()->getSession();
		$sesion->getFlashBag()->add('mensaje', 'El crédito se ha borrado correctamente');
	
		return $this->redirectToRoute('listar_donaciones');
	}
	
}