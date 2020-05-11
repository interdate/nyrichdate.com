<?php

namespace AppBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SignUpThreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('about', TextareaType::class, array(
            'label' => '* About Me <span style="display: inline-block;">(min 10 characters)</span>',
            'required' => true,
        ));

        $builder->add('looking', TextareaType::class, array(
            'label' => "* What I'm Looking For <span style=\"display: inline-block;\">(min 10 characters)</span>",
            'required' => true,
        ));

        $builder->add('hobbies', EntityType::class, array(
            'class' => 'AppBundle:Hobby',
            'label' => '* Hobbies',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
            'required' => true,
            'empty_data'  => null,
        ));
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults(array(
            'is_male' => false,
        ));
    }

}