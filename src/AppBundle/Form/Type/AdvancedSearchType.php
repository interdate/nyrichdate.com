<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class AdvancedSearchType extends QuickSearchSidebarType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('username');

        $builder->add('region', EntityType::class, array(
            'class' => 'AppBundle:Region',
            'label' => 'Area',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('area', EntityType::class, array(
            'class'       => 'AppBundle:Area',
            'label'       => ' ',
            'choices'     => array(),
        ));

        $addAreas = function (FormInterface $form, Region $region = null) {

            $areas = null === $region ? array() : $region->getAreas();

            $form->add('area', EntityType::class, array(
                'class' => 'AppBundle:Area',
                'label' => 'Neighborhood',
                'choice_label' => 'name',
                'choices'     => $areas,
                'multiple' => true,
                'expanded' => true,
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($addAreas) {
                $data = $event->getData();
                $form = $event->getForm();
                if(is_object($data)) {
                    $addAreas($form, $data->getRegion());
                }
            }
        );

        $builder->get('region')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($addAreas) {
                $region = $event->getForm()->getData();
                $addAreas($event->getForm()->getParent(), $region);
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use($addAreas){
                $data = $event->getData();
                $form = $event->getForm();
                $addAreas($form, $data->getRegion());
            }
        );




        /*
        $i = 0;
        $height = 1.2;
        $choices = array();

        while( (float) $height <= 2.2){
            $choices[(string) $height] = (string) $height;
            $height = (float) $height + 0.01;
            $i++;
        }
        */
        $choices = array();
        for($i = 54; $i <= 96; $i++){
            $heightStr = (int) ($i / 12) . "' " . ($i % 12) . "\" (" . round($i * 2.54) . " cm)";
            $choices[$heightStr] = round($i * 2.54);//$heightStr;
        }

        $builder->add('heightFrom', ChoiceType::class, array(
            'choices' => $choices,
            'placeholder' => 'Choose',
            'empty_data'  => null,
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('heightTo', ChoiceType::class, array(
            'choices' => $choices,
            'placeholder' => 'Choose',
            'empty_data'  => null,
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('body', EntityType::class, array(
            'class' => 'AppBundle:Body',
            'label' => 'Body Type',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('relationshipStatus', EntityType::class, array(
            'class' => 'AppBundle:RelationshipStatus',
            'label' => 'Marital Status',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('status', EntityType::class, array(
            'class' => 'AppBundle:Status',
            'label' => 'Status',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('netWorth', EntityType::class, array(
            'class' => 'AppBundle:NetWorth',
            'label' => 'Net Worth',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('income', EntityType::class, array(
            'class' => 'AppBundle:Income',
            'label' => 'Income',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('features', EntityType::class, array(
            'class' => 'AppBundle:Feature',
            'label' => 'Special Features',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('languages', EntityType::class, array(
            'class' => 'AppBundle:Language',
            'label' => 'Languages',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        /*$builder->add('occupation', TextType::class, array(
            'label' => 'תחום עיסוק',
        ));*/

        $builder->add('education', EntityType::class, array(
            'class' => 'AppBundle:Education',
            'label' => 'Education',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('religion', EntityType::class, array(
            'class' => 'AppBundle:Religion',
            'label' => 'Religion',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('purposes', EntityType::class, array(
            'class' => 'AppBundle:Purpose',
            'label' => 'Here For',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('smoking', EntityType::class, array(
            'class' => 'AppBundle:Smoking',
            'label' => 'Smoking',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('drinking', EntityType::class, array(
            'class' => 'AppBundle:Drinking',
            'label' => 'Drinking',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('children', ChoiceType::class, array(
            'label' => 'Children',
            'choices'  => array(
                '' => null,
                'No' => '1',
                'Yes' => '2',
            ),
        ));

        $builder->add('eyes', EntityType::class, array(
            'class' => 'AppBundle:Eyes',
            'label' => 'Eyes Color',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('hair', EntityType::class, array(
            'class' => 'AppBundle:Hair',
            'label' => 'Hair Color',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('features', EntityType::class, array(
            'class' => 'AppBundle:Feature',
            'label' => 'Special Features',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('hobbies', EntityType::class, array(
            'class' => 'AppBundle:Hobby',
            'label' => 'Hobbies',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('withPhoto', CheckboxType::class, array(
            'label' => 'With Profile Photo',
            'mapped' => false,
            'required' => false,
        ));

        if(!$options['do_not_create_ethnicity']){
            $builder->add('ethnicity', EntityType::class, array(
                'class' => 'AppBundle:Ethnicity',
                'label' => 'Ethnicity',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'mapped' => false,
            ));
        }

        if(!$options['do_not_create_zodiac']){
            $builder->add('zodiac', EntityType::class, array(
                'class' => 'AppBundle:Zodiac',
                'label' => 'Zodiac',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ));
        }

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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'do_not_create_ethnicity' => false,
            'do_not_create_zodiac' => false,
        ));
    }

}