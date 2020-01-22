<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Practice;
use App\Form\ActivityFormType;

class PracticeFormType extends ActivityFormType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Practice::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}