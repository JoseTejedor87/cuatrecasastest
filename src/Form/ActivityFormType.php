<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Entity\Activity;

abstract class ActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.activity.fields.title', 'required'=>true],
                    'slug' => ['label'=>'entities.activity.fields.slug'],
                    'summary' => ['label'=>'entities.activity.fields.summary', 'attr'=>['class'=>'summernote']],
                    'description' => ['label'=>'entities.activity.fields.description', 'attr'=>['class'=>'summernote']],
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription']
                ],
            ])
            ->add('relatedActivities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.activity.fields.relatedActivities',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            ->add('highlighted', CheckboxType::class, ['label'=>'entities.activity.fields.highlighted'])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('image', TextType::class, ['label'=>'entities.activity.fields.image']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin'
        ]);
    }
}
