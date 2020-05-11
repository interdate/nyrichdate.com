<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



class QuickSearchSidebarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array();
        for($i = 18; $i <= 120; $i++){
            $choices[(string) $i] = $i;
        }

        $builder->add('ageFrom', ChoiceType::class, array(
            'label' => 'Age',
            'choices' => $choices,
            'data' => 18,
            //'placeholder' => 'Choose',
            'empty_data'  => null,
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('ageTo', ChoiceType::class, array(
            'label' => 'to',
            'choices' => $choices,
            'data' => 35,
            //'placeholder' => 'Choose',
            'empty_data'  => null,
            'mapped' => false,
            'required' => false,
        ));


        $builder->add('region', EntityType::class, array(
            'class' => 'AppBundle:Region',
            'label' => '* Area',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));


        $builder->add('username', TextType::class, array(
            'label' => 'Username',
            'attr' => array('class' => 'txtbox'),
            'required' => false,
        ));

        $builder->add('filter', HiddenType::class, array(
            'data' => 'lastActivity',
            'mapped' => false,
        ));

        $choices = array();
        for($i=1; $i < 151; $i++){
            $choices[$i] = $i;
        }
        $builder->add('distance', ChoiceType::class, array(
            'choices' => $choices,
            'placeholder' => 'Choose',
            'mapped' => false,
            'required' => false,
        ));

    }


}