<?php

namespace AhoraMadrid\InscripcionInterventoresBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InscritoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('apellidos')
            ->add('documentoIdentidad')
            ->add('correoElectronico')
            ->add('telefono')
            ->add('profesion')
            ->add('edad')
            ->add('nacionalidad')
            ->add('direcccion')
            ->add('codigoPostal')
            ->add('distrito', 'entity', array('class' => 'AhoraMadridInscripcionInterventoresBundle:Distrito', 'property' => 'descripcion'))
            ->add('experienciaPrevia', 'choice', array(
            		'choices' => array(
            				'Interventora' => 'Interventor/a',
            				'Apoderada' => 'Apoderado/a',
            				'' => 'Ninguna',
            		),
            		'expanded' => true,
            ))
            ->add('escaneado')
            ->add('Enviar', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AhoraMadrid\InscripcionInterventoresBundle\Entity\Inscrito'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ahoramadrid_inscripcioninterventoresbundle_inscrito';
    }
}
