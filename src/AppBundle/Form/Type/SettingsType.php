<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reportEmail', TextType::class, array(
            'label' => 'Abuse report target email',
        ));

        $builder->add('contactEmail', TextType::class, array(
            'label' => 'Contact to target email',
        ));

        $builder->add('contactFromEmail', TextType::class, array(
        		'label' => 'Contact form target email',
        ));
        
        $builder->add('deleteMessagesAfterDaysNumber', TextType::class, array(
            'label' => 'MAXIMUM number of DAYS message will remain in the system BEFORE being DELETED',
            'required' => false,
        ));

        $builder->add('sendMessageUsersNumber', TextType::class, array(
            'label' => 'MAXIMUM number of MEMBERS a single member can address through site messages PER DAY',
            'required' => false,
        ));

        $builder->add('usersPerPage', TextType::class, array(
            'label' => 'Number of results on page on a Listing mode',
        ));

        $builder->add('isCharge', CheckboxType::class, array(
            'label' => 'Is the site in PAYING mode',
            'required' => false,
        ));

        $builder->add('messagePopularityDaysNumber', TextType::class, array(
            'label' => 'Oldest message in DAYS to measure user POPULARITY',
        ));

        $builder->add('userConsideredAsOnlineAfterLastActivityMinutesNumber', TextType::class, array(
            'label' => 'Number of minutes after last access to site member is still "ONLINE"',
        ));

        $builder->add('userConsideredAsNewAfterDaysNumber', TextType::class, array(
            'label' => 'Number of DAYS member is still in NEW status',
        ));

    }
    public function getName()
    {
        return 'settings';
    }

}