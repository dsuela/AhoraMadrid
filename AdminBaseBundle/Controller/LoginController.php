<?php

namespace AhoraMadrid\AdminBaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AhoraMadrid\AdminBaseBundle\Entity\Usuario;
use AhoraMadrid\AdminBaseBundle\Form\LoginType;

class LoginController extends AdminController{
	
	/**
	 * @Route("/", name="login")
	 */
	public function indexAction(Request $request){
		//Para hacer la contraseña <div>{{ contrasena }}</div>
		//$hash = password_hash('mtdStvJf', PASSWORD_BCRYPT, array('cost' => 8));
		
		//Se carga el formulario
		$usuarioParam = new Usuario();
		$form = $this->createForm(new LoginType(), $usuarioParam);
		$form->handleRequest($request);
		
		//Se hace el login
		$error = "";
		if ($form->isValid()) {
			//Se busca el usuario
			$repository = $this->getDoctrine()->getRepository('AhoraMadridAdminBaseBundle:Usuario');
			$usuario = $repository->findOneByCorreo($usuarioParam->getCorreo());
			
			if($usuario != null){
				//Si la contraseña es correccta, se guarda el usuario en la sesión y se redirige
				if (password_verify($usuarioParam->getContrasena(), $usuario->getContrasena())) {
					parent::guardarUsuarioSesion($request, $usuario);
					return $this->redirectToRoute('menu_admin');
				} else {
					$error = "Contraseña incorrecta";
				}
			} else {
				$error = "Usuario incorrecto";
			}
		}
		
		return $this->render('AhoraMadridAdminBaseBundle::login.html.twig', array('form' => $form->createView(), 'error' => $error));
		//return $this->render('AhoraMadridAdminBaseBundle:login.html.twig', array('form' => $form->createView(), 'error' => $error, 'contrasena' => $hash));
	}
	
	/**
	 * @Route("/logout", name="logout")
	 */
	public function logout(Request $request){
		//Se destruye la sesión
		$sesion = $request->getSession();
		$sesion->clear();
		
		//Se redirige al login
		return $this->redirectToRoute('login');
	}
	
	/**
	 * @Route("/menu", name="menu_admin")
	 */
	public function menu(Request $request){
		return $this->render('AhoraMadridAdminBaseBundle::menu.html.twig');
	}
	
}