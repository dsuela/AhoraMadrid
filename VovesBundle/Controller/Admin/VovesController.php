<?php

namespace AhoraMadrid\VovesBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AhoraMadrid\AdminBaseBundle\Controller\AdminController as AdminController;
use AhoraMadrid\VovesBundle\Entity\Votante;

class VovesController extends AdminController{
	
	/**
	 * @Route("/apladmin/listar-votantes", name="listar_votantes")
	 */
	public function listarVotantes(Request $request){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_VOTANTES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se crea y se recoge el formulario de búsqueda
		$form = $this->createFormBuilder()
		->setMethod('GET')
		->add('documentoIdentidad', 'text')
		->add('nombre', 'text')
		->add('apellidos', 'text')
		->add('distrito', 'entity', array('class' => 'AhoraMadridInscripcionInterventoresBundle:Distrito', 'property' => 'descripcion', 'placeholder' => 'Todos'))
		->add('direcccion', 'text')
		->add('Buscar', 'submit')
		->getForm();
	
		$form->handleRequest($request);
		 
		$data = $form->getData();
	
		//Se buscan los inscritos
		$repository = $this->getDoctrine()->getRepository('AhoraMadridVovesBundle:Votante');
		$qb = $repository->createQueryBuilder('c')
		->orderBy('c.id', 'DESC');
	
		//Se añaden al queryBuilder las opciones del buscador, si las hay
		if($data['documentoIdentidad'] != null && !empty(trim($data['documentoIdentidad']))){
			$qb->andWhere('c.documentoIdentidad LIKE :documentoIdentidad')
			->setParameter('documentoIdentidad', '%'.$data['documentoIdentidad'].'%');
		}
	
		if($data['nombre'] != null && !empty(trim($data['nombre']))){
			$qb->andWhere('c.nombre LIKE :nombre')
			->setParameter('nombre', '%'.$data['nombre'].'%');
		}
	
		if($data['apellidos'] != null && !empty(trim($data['apellidos']))){
			$qb->andWhere('c.apellidos LIKE :apellidos')
			->setParameter('apellidos', '%'.$data['apellidos'].'%');
		}
	
		if($data['distrito'] != null && !empty($data['distrito'])){
			$qb->andWhere('c.distrito = :distrito')
			->setParameter('distrito', $data['distrito']);
		}
		
		if($data['direcccion'] != null && !empty(trim($data['direcccion']))){
			$qb->andWhere('c.direcccion LIKE :direcccion')
			->setParameter('direcccion', '%'.$data['direcccion'].'%');
		}
	
		$query = $qb->getQuery();
	
		$votantes = $query->getResult();
	
		//Paginación
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$votantes, $this->get('request')->query->get('page', 1), 25
		);
	
		return $this->render('AhoraMadridVovesBundle:Admin:listar_votantes.html.twig', array(
				'form' => $form->createView(),
				'pagination' => $pagination
		));
	}
	
	/**
	 * @Route("/apladmin/detalle-votante/{id}", name="detalle_votante")
	 */
	public function detalleVotante(Request $request, $id){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_VOTANTES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se busca el inscrito
		$repository = $this->getDoctrine()->getRepository('AhoraMadridVovesBundle:Votante');
		$votante = $repository->find($id);
	
		return $this->render('AhoraMadridVovesBundle:Admin:detalle_votante.html.twig', array('votante' => $votante));
	}
	
	/**
	 * @Route("/apladmin/validar-votante/{id}/{validar}", name="validar_votante")
	 */
	public function validarVotante(Request $request, $id, $validar){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_VOTANTES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se busca el inscrito y se cambia de estado
		$em = $this->getDoctrine()->getManager();
		$votante = $em->getRepository('AhoraMadridVovesBundle:Votante')->find($id);
		$votante->setValidada($validar);
		$em->flush();
	
		//Se guarda el mensaje
		$sesion = $this->getRequest()->getSession();
		$sesion->getFlashBag()->add('mensaje', 'La validación de votante ha cambiado de estado correctamente');
	
		return $this->redirectToRoute('listar_votantes');
	}
	
	/**
	 * @Route("/apladmin/validar-voto/{id}/{validar}", name="validar_voto")
	 */
	public function validarVoto(Request $request, $id, $validar){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_VOTANTES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se buscan el inscrito y se borra
		$em = $this->getDoctrine()->getManager();
		$votante = $em->getRepository('AhoraMadridVovesBundle:Votante')->find($id);
		
		//Solo pueden votar los validados
		if($votante->getValidada()){
			$votante->setVotado($validar);
			$em->flush();
			
			//Se guarda el mensaje
			$sesion = $this->getRequest()->getSession();
			$sesion->getFlashBag()->add('mensaje', 'La validación de voto ha cambiado de estado correctamente');
		} else {
			//Se guarda el mensaje
			$sesion = $this->getRequest()->getSession();
			$sesion->getFlashBag()->add('mensaje', 'No puede votar porque no ha sido validada');
		}
	
		return $this->redirectToRoute('listar_votantes');
	}
	
}