<?php

namespace AhoraMadrid\InscripcionInterventoresBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AhoraMadrid\InscripcionInterventoresBundle\Entity\Inscrito;
use AhoraMadrid\InscripcionInterventoresBundle\Form\InscritoType;

class DefaultController extends Controller{
    /**
     * @Route("/inscribirse", name="inicio_inscritos")
     * @Template()
     */
    public function indexAction(Request $request){
        //Se carga el formulario
		$inscrito = new Inscrito();
		$form = $this->createForm(new InscritoType(), $inscrito);
		$form->handleRequest($request);
		
		//Si la validación es correcta, se persiste el inscrito y se redirige a mostrar el correcto
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			$inscrito->upload();
			
			$em->persist($inscrito);
			$em->flush();
			
			return $this->redirectToRoute('inscripcion_ok');
		}
		
		//Si no se ha enviado el formulario, se carga la página con el formulario
		return $this->render('AhoraMadridInscripcionInterventoresBundle:Default:inscripcion.html.twig', array(
				'form' => $form->createView(),
		));
    }
    
    /**
     * @Route("/inscripcion-realizada", name="inscripcion_ok")
     */
    public function contrato(Request $request){
    	return $this->render('AhoraMadridInscripcionInterventoresBundle:Default:inscripcion_ok.html.twig');
    }
}
