<?php

// src/Form/RemoveFromFavouriteType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoveFromFavouriteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['action'])
            ->setMethod('POST')
            ->add('save', SubmitType::class, [
                'label' => 'Remove from favourites',
                'attr' => [
                    'onclick' => 'submitForm(event); return false;',
                ]
			]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => '',
        ]);
    }
}
