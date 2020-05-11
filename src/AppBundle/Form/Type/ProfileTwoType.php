<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProfileTwoType extends SignUpTwoType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('profile_two'),
            'is_male' => false,
        ));
    }

    public function getName()
    {
        return 'profileTwo';
    }

}