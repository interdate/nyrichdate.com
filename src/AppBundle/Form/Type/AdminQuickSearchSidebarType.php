<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminQuickSearchSidebarType extends QuickSearchSidebarType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('region', EntityType::class, array(
            'class' => 'AppBundle:Region',
            'label' => 'Area',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('id', TextType::class, array(
            'label' => 'ID',
            'attr' => array('placeholder' =>  'ID'),
            'required' => false,
        ));

        $builder->add('email', TextType::class, array(
            'label' => 'Email',
            'attr' => array('placeholder' =>  'Email'),
            'required' => false,
        ));

        $builder->add('username', TextType::class, array(
            'label' => 'Username',
            'attr' => array('placeholder' => 'Username'),
            'required' => false,
        ));

        $builder->add('phone', TextType::class, array(
            'label' => 'Phone',
            'attr' => array('placeholder' => 'Phone'),
            'required' => false,
        ));

    }
}