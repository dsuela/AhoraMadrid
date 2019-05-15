<?php

namespace MadridEnPie\AdminBaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use MadridEnPie\MicrocreditosBundle\Entity\Usuario;

class AdminController extends Controller{
	
	const ROL_SUPER_USUARIO = 1;
	const ROL_CONSULTA = 10;
	const ROL_ADMIN = 20;
	const ROL_ADMIN_INTERVENTORES = 30;
	const ROL_CONSULTA_INTERVENTORES = 40;
	const ROL_ADMIN_VOTANTES = 50;
	
	protected function guardarUsuarioSesion(Request $request, $usuario){
		if($usuario != null){
			$sesion = $request->getSession();
			$sesion->set('usuario', $usuario->getNombre() . $usuario->getApellidos());
			$sesion->set('rol', $usuario->getRol()->getId());
		}
	}
	
	protected function controlSesion(Request $request, $roles) {
		$sesion = $request->getSession();
		
		//Si no hay usuario en la sesi�n o el usuario no tiene rol, se sale (! compara nulo y vac�o)
		if($sesion->get('usuario') == null || $sesion->get('rol') == null){
			return self::prohibido();
		}
		
		//Si no se piden roles, se permite el acceso
		if($roles == null){
			return null;
		}
		
		//Si alguno de los roles que se piden coincide con el del usuario, se permite el acceso
		foreach ((array) $roles as $rol){
			if($sesion->get('rol') == $rol){
				return null;
			}
		}
		
		//Si ninguno ha coincidido, se sale
		return self::prohibido();
	}
	
	protected function prohibido(){
		return $this->render('MadridEnPieAdminBaseBundle::prohibido.html.twig');
	}
	
}