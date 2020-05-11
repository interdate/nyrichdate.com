<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminAdvancedSearchType extends AdvancedSearchType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('region', EntityType::class, array(
            'class' => 'AppBundle:Region',
            'label' => 'Area',
            'choice_label' => 'name',
            'empty_data'  => null,
            'multiple' => true,
            'expanded' => true,
            'mapped' => false,
        ));

/*        $builder->add('zipCode', EntityType::class, array(
            'class' => 'AppBundle:ZipCode',
            'choice_label' => 'name',
            'empty_data'  => null,
            'choice_value' => 'name',
        ));*/

        $builder->add('zipCodeSingle', HiddenType::class, array('mapped' => false));

        $builder->add('zodiac', EntityType::class, array(
            'class' => 'AppBundle:Zodiac',
            'label' => 'Zodiac',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('loginFrom', EntityType::class, array(
            'class' => 'AppBundle:LoginFrom',
            'label' => 'Last Login From',
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));


        /*Boolean Props*/

        $builder->add('isActive', ChoiceType::class, array(
            'label' => 'Active',
            'choices'  => array(
                'Choose' => null,
                'Yes' => true,
                'No' => false,
            ),
            'mapped' => false,
            'required' => false,
        ));

        //isActivated - phone activate

        $builder->add('isActivated', ChoiceType::class, array(
            'label' => 'Phone Activated',
            'choices'  => array(
                'Choose' => null,
                'Yes' => true,
                'No' => false,
            ),
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('isFrozen', ChoiceType::class, array(
            'label' => 'Frozen',
            'choices'  => array(
                'Choose' => null,
                'Yes' => true,
                'No' => false,
            ),
            'mapped' => false,
            'required' => false,
        ));


        $builder->add('isPhoto', ChoiceType::class, array(
            'label' => 'With Photo',
            'choices'  => array(
                'Choose' => null,
                'Yes' => true,
                'No' => false,
            ),
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('isPaying', ChoiceType::class, array(
            'label' => 'Paying',
            'choices'  => array(
                'Choose' => null,
                'Yes' => true,
                'No' => false,
            ),
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('hasPoints', ChoiceType::class, array(
            'label' => 'With Points',
            'choices'  => array(
                'Choose' => null,
                'Yes' => true,
                'No' => false,
            ),
            'mapped' => false,
            'required' => false,
        ));

        /*
        $builder->add('isPhone', ChoiceType::class, array(
            'label' => "With Phone",
            'choices'  => array(
                'Choose' => null,
                'Yes' => true,
                'No' => false,
            ),
            'mapped' => false,
            'required' => false,
        ));*/

        /* Date Props */

        $builder->add('startSubscriptionFrom', TextType::class, array(
            'label' => 'Start Paying Date',
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('startSubscriptionTo', TextType::class, array(
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('endSubscriptionFrom', TextType::class, array(
            'label' => 'End Paying Date',
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('endSubscriptionTo', TextType::class, array(
            'mapped' => false,
            'required' => false,
        ));


        $builder->add('signUpFrom', TextType::class, array(
            'label' => 'Sign Up Date',
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('signUpTo', TextType::class, array(
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('lastVisitedFrom', TextType::class, array(
            'label' => 'Last Visit Date',
            'mapped' => false,
            'required' => false,
        ));

        $builder->add('lastVisitedTo', TextType::class, array(
            'mapped' => false,
            'required' => false,
        ));


        /* Other */

        $builder->add('ip', 'text', array(
            'label' => 'IP',
            'required' => false,
        ));

        $builder->add('gender', EntityType::class, array(
            'class' => 'AppBundle:Gender',
            'label' => 'Gender:',
            'choice_label' => 'name',
            'placeholder' => 'Choose',
            'empty_data'  => null,
            'required' => false,
        ));



    }
}