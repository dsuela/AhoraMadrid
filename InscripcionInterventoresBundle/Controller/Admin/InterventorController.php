<?php

namespace AhoraMadrid\InscripcionInterventoresBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AhoraMadrid\AdminBaseBundle\Controller\AdminController as AdminController;
use AhoraMadrid\InscripcionInterventoresBundle\Entity\Inscrito;

class InterventorController extends AdminController{
	
	/**
	 * @Route("/apladmin/listar-inscritos", name="listar_inscritos")
	 */
	public function listarInscritos(Request $request){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_INTERVENTORES, parent::ROL_CONSULTA_INTERVENTORES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se crea y se recoge el formulario de búsqueda
		$form = $this->createFormBuilder()
		->setMethod('GET')
		->add('documentoIdentidad', 'text')
		->add('nombre', 'text')
		->add('apellidos', 'text')
		->add('distrito', 'entity', array('class' => 'AhoraMadridInscripcionInterventoresBundle:Distrito', 'property' => 'descripcion', 'placeholder' => 'Todos'))
		->add('aprobada', 'choice', array('choices' => array('0' => 'No', '1' => 'Sí', '' => 'Todas'), 'expanded' => true))
		->add('Buscar', 'submit')
		->getForm();
	
		$form->handleRequest($request);
		 
		$data = $form->getData();
	
		//Se buscan los inscritos
		$repository = $this->getDoctrine()->getRepository('AhoraMadridInscripcionInterventoresBundle:Inscrito');
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
		
		if($data['aprobada'] != null && !empty(trim($data['aprobada']))){
			$qb->andWhere('c.aprobada = 1');
		}
	
		$query = $qb->getQuery();
	
		$inscritos = $query->getResult();
	
		//Se buscan los inscritos aprobados
		$qbRecibidos = $repository->createQueryBuilder('c')
		->select('COUNT(c.id)')
		->where('c.aprobada = 1');
	
		$aprobados = $qbRecibidos->getQuery()->getSingleScalarResult();
	
		//Paginación
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$inscritos, $this->get('request')->query->get('page', 1), 25
		);
	
		return $this->render('AhoraMadridInscripcionInterventoresBundle:Admin:listar_inscritos.html.twig', array(
				'form' => $form->createView(),
				'pagination' => $pagination,
				'aprobados' => $aprobados
		));
	}
	
	/**
	 * @Route("/apladmin/detalle-inscrito/{id}", name="detalle_inscrito")
	 */
	public function detalleCredito(Request $request, $id){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_INTERVENTORES, parent::ROL_CONSULTA_INTERVENTORES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se busca el inscrito
		$repository = $this->getDoctrine()->getRepository('AhoraMadridInscripcionInterventoresBundle:Inscrito');
		$inscrito = $repository->find($id);
	
		return $this->render('AhoraMadridInscripcionInterventoresBundle:Admin:detalle_inscrito.html.twig', array('inscrito' => $inscrito));
	}
	
	/**
	 * @Route("/apladmin/aprobar-inscrito/{id}/{aprobar}", name="aprobar_inscrito")
	 */
	public function aprobarInscrito(Request $request, $id, $aprobar){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_INTERVENTORES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se busca el inscrito y se cambia de estado
		$em = $this->getDoctrine()->getManager();
		$inscrito = $em->getRepository('AhoraMadridInscripcionInterventoresBundle:Inscrito')->find($id);
		$inscrito->setAprobada($aprobar);
		$em->flush();
	
		//Se guarda el mensaje
		$sesion = $this->getRequest()->getSession();
		$sesion->getFlashBag()->add('mensaje', 'La inscrita se ha cambiado de estado correctamente');
	
		return $this->redirectToRoute('listar_inscritos');
	}
	
	/**
	 * @Route("/apladmin/borrar-inscrito/{id}", name="borrar_inscrito")
	 */
	public function borrarInscrito(Request $request, $id){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_INTERVENTORES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
	
		//Se buscan el inscrito y se borra
		$em = $this->getDoctrine()->getManager();
		$inscrito = $em->getRepository('AhoraMadridInscripcionInterventoresBundle:Inscrito')->find($id);
		$em->remove($inscrito);
		$em->flush();
	
		//Se guarda el mensaje
		$sesion = $this->getRequest()->getSession();
		$sesion->getFlashBag()->add('mensaje', 'La inscrita se ha borrado correctamente');
	
		return $this->redirectToRoute('listar_inscritos');
	}
	
	/**
	 * @Route("/apladmin/exportar-csv/", name="csv_interventores")
	 */
	public function exportarCsv(Request $request){
		//Control de roles
		$response = parent::controlSesion($request, array(parent::ROL_ADMIN_INTERVENTORES, parent::ROL_CONSULTA_INTERVENTORES, parent::ROL_SUPER_USUARIO));
		if($response != null) return $response;
		
		//Se crea la respuesta
		$response = new StreamedResponse();
		$response->setCallback(function () {
			ob_flush();
			flush();
			
			//Se buscan los inscritos
			$repository = $this->getDoctrine()->getRepository('AhoraMadridInscripcionInterventoresBundle:Inscrito');
			$inscritos = $repository->findAll();
			
			$fp = fopen('php://output', 'w');
			
			fputcsv($fp, array('Id', 'Distrito', 'Nombre', 'Apellidos', 'Documento identidad', 
					'Correo', 'Teléfono', 'Profesión', 'Edad', 'Nacionalidad', 'Dirección', 
					'Código postal', 'Experiencia previa', 'Aprobada'), ';');
			
			foreach($inscritos as $inscrito){
				fputcsv($fp, array($inscrito->getId(), $inscrito->getDistrito()->getDescripcion(), $inscrito->getNombre(), 
						$inscrito->getApellidos(), $inscrito->getDocumentoIdentidad(), $inscrito->getCorreoElectronico(), 
						$inscrito->getTelefono(), $inscrito->getProfesion(), $inscrito->getEdad(), $inscrito->getNacionalidad(), 
						$inscrito->getDirecccion(), $inscrito->getCodigoPostal(), $inscrito->getExperienciaPrevia(), 
						$inscrito->getAprobada() == true ? 'Si' : 'No'), ';');
			}
			fclose($fp);
			
			
			
			ob_flush();
			flush();
		});
		
		//$response->headers->set('Content-Type', 'application/force-download');
		$response->headers->set('Content-Disposition','attachment; filename="apoderadas.csv"');
		$response->headers->set('Content-Type', 'text/csv');
		$response->setCharset('ISO-8859-1');
		
		return $response;
		//return new Response($fp, 200, array('content-type' => 'text/csv'));
	}
	
}