<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPropertiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isOnHomepage', CheckboxType::class, array(
            'label' => 'User shown on the homepage',
            'required' => false,
        ));

        $builder->add('adminComments', TextareaType::class, array(
            'label' => 'Comments',
            'required' => false,
        ));

        $builder->add('banReason', TextareaType::class, array(
            'label' => 'Inactivation Reason',
            'required' => false,
        ));

        $builder->add('freezeReason', TextareaType::class, array(
            'label' => 'Freezing Reason',
            'required' => false,
        ));

        $builder->add('isSentEmail', CheckboxType::class, array(
            'label'    => 'Sent to user notification on E-mail?',
            'required' => false,
        ));

        $builder->add('isSentPush', CheckboxType::class, array(
            'label'    => 'Sent to user push notification?',
            'required' => false,
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'is_male' => false,
        ));
    }

}