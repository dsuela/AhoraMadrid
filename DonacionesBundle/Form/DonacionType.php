<?php

namespace AhoraMadrid\DonacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AhoraMadrid\DonacionesBundle\Form\ReCaptchaType;

class DonacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre')
                ->add('apellidos')
                ->add('documentoIdentidad')
                ->add('correoElectronico')
                ->add('pais')
                ->add('provincia')
                ->add('municipio')
                ->add('codigoPostal')
                ->add('direcccion')
                ->add('importe', 'choice', array(
                    'choices' => array(
                        '5' => '5 €',
                        '10' => '10 €',
                        '25' => '25 €',
                        '50' => '50 €',
                        '100' => '100 €',
                        '300' => '300 €',
                        '500' => '500 €',
                        '1000' => '1.000 €',
                        '2000' => '2.000 €',
                        '5000' => '5.000 €',
                        '10000' => '10.000 €',
                    ),
                    'expanded' => true,
                ))
                //->add('captcha', 're_captcha', array('mapped' => false))
                ->add('Enviar', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AhoraMadrid\DonacionesBundle\Entity\Donacion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'ahoramadrid_donacionesbundle_donacion';
    }

}
