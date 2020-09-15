<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use App\Entity\Slider;
use App\Entity\Banner;
use App\Form\ResourceFormType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

class BannerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sliders', EntityType::class, [
                'class' => Slider::class,
                'label' => 'entities.banner.fields.slider',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choice_label' => function ($slider) {
                    return $slider->translate('es')->getTitle();
                }
            ])
            ->add('location', TextType::class, ['label'=>'Location Page'])
            ->add('speed', IntegerType::class, [
                'label'=>'Velocidad de transicion ( defecto 800ms)',
                 'required' => true]
            )
            ->add('delay', IntegerType::class, [
                'label'=>'Retraso cambio automatico ( defecto 5000ms)',
                'required' => true]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
