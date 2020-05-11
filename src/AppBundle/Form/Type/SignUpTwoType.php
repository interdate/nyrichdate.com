<?php

namespace AppBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SignUpTwoType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('relationshipStatus', EntityType::class, array(
            'class' => 'AppBundle:RelationshipStatus',
            'label' => '* Relationship Status',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('children', EntityType::class, array(
            'class' => 'AppBundle:Children',
            'label' => '* Children',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('ethnicity', EntityType::class, array(
            'class' => 'AppBundle:Ethnicity',
            'label' => '* Ethnicity',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('religion', EntityType::class, array(
            'class' => 'AppBundle:Religion',
            'label' => '* Religion',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('languages', EntityType::class, array(
            'class' => 'AppBundle:Language',
            'label' => '* Language',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
            'required' => true,
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('education', EntityType::class, array(
            'class' => 'AppBundle:Education',
            'label' => '* Education',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('occupation', TextType::class, array(
            'label' => 'Occupation',
            'required' => false,
        ));


        $builder->add('smoking', EntityType::class, array(
            'class' => 'AppBundle:Smoking',
            'label' => '* Smoking',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('drinking', EntityType::class, array(
            'class' => 'AppBundle:Drinking',
            'label' => '* Drinking',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));


        $builder->add('purposes', EntityType::class, array(
            'class' => 'AppBundle:Purpose',
            'label' => '* Here For',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
            'required' => true,
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));





        if($options['is_male']){
            $builder->add('status', EntityType::class, array(
                'class' => 'AppBundle:Status',
                'label' => '* Status',
                'choice_label' => 'name',
                'placeholder' => 'Choose',
                'empty_data'  => null,
            ));

            $builder->add('netWorth', EntityType::class, array(
                'class' => 'AppBundle:NetWorth',
                'label' => '* Net Worth',
                'choice_label' => 'name',
                'placeholder' => 'Choose',
                'empty_data'  => null,
            ));

            $builder->add('income', EntityType::class, array(
                'class' => 'AppBundle:Income',
                'label' => '* Income',
                'choice_label' => 'name',
                'placeholder' => 'Choose',
                'empty_data'  => null,
            ));
        }

        /*
        $i = 0;
        $height = 1.2;
        $choices = array();

        //$cf_m = 0.0254;

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

        $builder->add('height', ChoiceType::class, array(
            'label' => '* Height',
            'choices' => $choices,
            'placeholder' => 'Choose',
        ));

        $builder->add('body', EntityType::class, array(
            'class' => 'AppBundle:Body',
            'label' => '* Body Type',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('eyes', EntityType::class, array(
            'class' => 'AppBundle:Eyes',
            'label' => '* Eye Color',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));

        $builder->add('hair', EntityType::class, array(
            'class' => 'AppBundle:Hair',
            'label' => '* Hair Color',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
        ));


        $builder->add('features', EntityType::class, array(
            'class' => 'AppBundle:Feature',
            'label' => 'Special Features',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
            'required' => false,
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