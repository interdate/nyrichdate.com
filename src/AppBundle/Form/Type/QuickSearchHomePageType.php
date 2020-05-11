<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class QuickSearchHomePageType extends QuickSearchSidebarType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);

        //Overriding
        $builder->remove('username');
        $builder->remove('distance');
        $builder->add('region', EntityType::class, array(
            'class' => 'AppBundle:Region',
            'label' => 'In:',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
            'required' => false,
        ));

        //New Items

        $builder->add('gender', EntityType::class, array(
            'class' => 'AppBundle:Gender',
            'label' => 'Seeking a:',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));
    }

    public function getName()
    {
        return 'quickSearchHomePage';
    }

}