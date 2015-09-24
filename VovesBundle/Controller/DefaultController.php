<?php

namespace AhoraMadrid\VovesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AhoraMadrid\VovesBundle\Entity\Votante;
use AhoraMadrid\VovesBundle\Form\VotanteType;

class DefaultController extends Controller
{
    /**
     * @Route("/inscripcion")
     * @Template()
     */
    public function indexAction(Request $request){
        //Se carga el formulario
		$votante = new Votante();
		$form = $this->createForm(new VotanteType(), $votante);
		$form->handleRequest($request);
		
		//Si la validación es correcta, se persiste el inscrito y se redirige a mostrar el correcto
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			$em->persist($votante);
			$em->flush();
			
			return $this->redirectToRoute('inscripcion_ok');
		}
		
		//Si no se ha enviado el formulario, se carga la página con el formulario
		return $this->render('AhoraMadridVovesBundle:Default:inscripcion.html.twig', array(
				'form' => $form->createView(),
		));
    }
    
    /**
     * @Route("/inscripcion-realizada", name="inscripcion_ok")
     */
    public function correcto(Request $request){
    	return $this->render('AhoraMadridVovesBundle:Default:inscripcion_ok.html.twig');
    }

    /**
     * @Route("/", name="vocalia_home")
     */
    public function homeAction(Request $request){
        return $this->render('AhoraMadridVovesBundle:Default:home.html.twig');
    }
}
