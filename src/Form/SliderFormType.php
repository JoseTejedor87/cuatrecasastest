<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Entity\Slider;
use App\Form\ResourceFormType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

class SliderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', ResourceFormType::class, [
                'required' => false,
                'label'=>'entities.slider.fields.image'
            ])
            ->add('priority', IntegerType::class, ['label'=>'entities.slider.fields.priority'])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'title' => ['label'=>'entities.slider.fields.title', 'required' => true, 'empty_data' => ''],
                    'description' => ['label'=>'entities.slider.fields.description', 'required' => true, 'empty_data' => ''],
                    'url' => ['label'=>'entities.slider.fields.url', 'required' => true, 'empty_data' => ''],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slider::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
