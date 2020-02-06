<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Product;
use App\Form\ActivityFormType;

class ProductFormType extends ActivityFormType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
