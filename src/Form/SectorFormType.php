<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Sector;
use App\Form\ActivityFormType;

class SectorFormType extends ActivityFormType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sector::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
