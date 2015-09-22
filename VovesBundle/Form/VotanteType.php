<?php

namespace AhoraMadrid\VovesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VotanteType extends AbstractType
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
            ->add('direcccion')
            ->add('codigoPostal')
            ->add('poblacion')
            ->add('distrito', 'entity', array('class' => 'AhoraMadridInscripcionInterventoresBundle:Distrito', 'property' => 'descripcion'))
            ->add('recibirInformacion', 'choice', array(
            		'choices' => array(
            				'1' => 'Sí',
            				'0' => 'No',
            		),
            		'expanded' => true,
            ))
            ->add('Enviar', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AhoraMadrid\VovesBundle\Entity\Votante'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ahoramadrid_vovesbundle_votante';
    }
}
