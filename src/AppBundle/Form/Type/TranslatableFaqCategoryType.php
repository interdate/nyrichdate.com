<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TranslatableFaqCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder->add('categories', 'a2lix_translatedEntity', array(
            'class' => 'AppBundle\Entity\FaqCategory',
            'translation_property' => 'text',
            'label' => 'FAQ Categories',
            'choice_label' => 'name',
            'placeholder' => 'בחרו',
            'empty_data'  => null,
        ));*/

        $builder->add('translations', 'a2lix_translations');
    }
    public function getName()
    {
        return 'TranslatableFaqCategory';
    }

}