<?php

namespace MadridEnPie\AdminBaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoginType extends AbstractType{
	
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder
			->add('correo')
			->add('contrasena', 'password')
			->add('Enviar', 'submit')
		;
	}
	
	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'MadridEnPie\AdminBaseBundle\Entity\Usuario'
		));
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'ahoramadrid_adminbasebundle_usuario';
	}
}