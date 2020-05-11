<?php

namespace AppBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'label' => 'Header',
        ));

        $builder->add('headerType', ChoiceType::class, array(
            'label' => 'Header Type',
            'choices'  => array(
                'H1' => 'h1',
                'H2' => 'h2',
            ),
            'choices_as_values' => true,
        ));

        $builder->add('content', TextareaType::class, array(
            'label' => 'Content',
            'required' => false,
        ));



        $builder->add('uri', TextType::class, array(
            'label' => 'URI',
            'required' => false,
        ));

        $builder->add('title', TextType::class, array(
            'label' => 'Title',
        ));

        $builder->add('description', TextareaType::class, array(
            'label' => 'Meta Description',
            'required' => false,
        ));

        $builder->add('isActive', CheckboxType::class, array(
            'label' => 'Active',
            'required' => false,
        ));

        $builder->add('footerHeader', EntityType::class, array(
            'class' => 'AppBundle:FooterHeader',
            'label' => 'Shown in footer under',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'multiple' => false,
            'expanded' => false,
            'required' => false,
        ));


    }
    public function getName()
    {
        return 'page';
    }

}