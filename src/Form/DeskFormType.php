<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Desk;
use App\Form\ActivityFormType;

class DeskFormType extends ActivityFormType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Desk::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
