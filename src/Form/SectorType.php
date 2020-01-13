<?php

namespace App\Form;

use App\Entity\Sector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('translations', TranslationsType::class, [
            'fields' => [                               
                'title' => ['attr' => array('class' => 'form-control m-input')],
                'description' => ['attr' => array('class' => 'form-control m-input')],
                'experience' => ['attr' => array('class' => 'form-control m-input')],
                'tags' => ['attr' => array('class' => 'form-control m-input')],
                'url_image' => ['attr' => array('class' => 'form-control m-input')],
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sector::class,
        ]);
    }
}
