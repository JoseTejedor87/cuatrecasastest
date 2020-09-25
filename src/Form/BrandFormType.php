<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Entity\Brand;

class BrandFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'description' => [
                        'label'=>'entities.brand.fields.description',
                        'required'=>true,
                        'attr'=>['class'=>'summernote']
                    ],
                ],
            ])
            ->add('image', ResourceFormType::class, ['label'=>'entities.brand.fields.image'])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published', 'value' => true])
            ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Brand::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
