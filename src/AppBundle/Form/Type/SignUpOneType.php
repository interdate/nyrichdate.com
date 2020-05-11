<?php

namespace AppBundle\Form\Type;


use AppBundle\Entity\Area;
use AppBundle\Entity\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class SignUpOneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $event->stopPropagation();
        }, 900);*/


        $builder->add('username', TextType::class, array(
            'label' => '* Username',
        ));

        $builder->add('password', PasswordType::class, array(
            'label' => '* Password',
        ));

        $builder->add('phone', TextType::class, array(
            'label' => "Phone",
            'required' => false,
        ));

        $builder->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => "Passwords don't match",
            'required' => true,
            'first_options'  => array('label' => '* Password'),
            'second_options' => array('label' => '* Retype Password'),
        ));

        $builder->add('email', RepeatedType::class, array(
            'type' => TextType::class,
            'invalid_message' => "Emails don't match",
            'required' => true,
            'first_options'  => array('label' => '* Email'),
            'second_options' => array('label' => '* Retype Email'),
        ));

        $builder->add('gender', EntityType::class, array(
            'class' => 'AppBundle:Gender',
            'label' => "* I'm a",
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('birthday', DateType::class, array(
            'label' => '* Birthday',
            'years' => range(date('Y') - 18, date('Y') - 120),
            'placeholder' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'),
            'empty_data'  => null,
        ));

        $builder->add('region', EntityType::class, array(
            'class' => 'AppBundle:Region',
            'label' => '* Area',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('area', EntityType::class, array(
            'class'       => 'AppBundle:Area',
            'choices'     => array(),
        ));

        $builder->add('zipCode', EntityType::class, array(
            'class' => 'AppBundle:ZipCode',
            'choices' => array(),
        ));

        $builder->add('agree', CheckboxType::class, array(
            'mapped' => false,
            'attr' => array('id' => 'x1'),
        ));

        $addZipCodes = function (FormInterface $form, Area $area = null) {

            $zipCodes = null === $area ? array() : $area->getZipCodes();

            $form->add('zipCode', EntityType::class, array(
                'class' => 'AppBundle:ZipCode',
                'label' => '* Zip Code',
                'choice_label' => 'name',
                'placeholder' => 'Choose',
                'choices'     => $zipCodes,
                'empty_data'  => null,
            ));
        };

        $addAreas = function (FormInterface $form, Region $region = null) {

            $areas = null === $region ? array() : $region->getAreas();

            $form->add('area', EntityType::class, array(
                'class'       => 'AppBundle:Area',
                'label' => '* Neighborhood',
                'choice_label' => 'name',
                'placeholder' => 'Choose',
                'choices'     => $areas,
                'empty_data'  => null,
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($addAreas, $addZipCodes) {
                $data = $event->getData();
                $form = $event->getForm();
                $addAreas($form, $data->getRegion());
                $addZipCodes($form, $data->getArea());
            }
        );


        $builder->get('region')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($addAreas) {
                $region = $event->getForm()->getData();
                $addAreas($event->getForm()->getParent(), $region);
            }
        );

        $builder->get('area')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($addZipCodes) {
                $area = $event->getForm()->getData();
                $addZipCodes($event->getForm()->getParent(), $area);
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use($addZipCodes, $addAreas){
                $data = $event->getData();
                $form = $event->getForm();
                $addAreas($form, $data->getRegion());
                $addZipCodes( $form, $data->getArea());
            }
        );


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('sign_up_one'),
        ));
    }


}