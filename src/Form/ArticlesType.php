<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

use App\Entity\Activity;
use App\Entity\Lawyer;
use App\Entity\Office;
use App\Form\Type\LanguageType;
use App\Form\ResourceFormType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('status')
            ->add('featured')
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.lawyer.fields.activities',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            ->add('offices', EntityType::class, [
                'class' => Office::class,
                'label' => 'entities.lawyer.fields.office',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($office) {
                    return $office->translate('es')->getCity();
                }
            ])
            ->add('lawyers', EntityType::class, [
                'class' => Lawyer::class,
                'label' => 'entities.speaker.fields.lawyer',
                'help' => 'entities.speaker.help.lawyer',
                'placeholder' => '',
                'attr' => [
                    'class' => 'm-select2'
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($lawyer) {
                    return $lawyer->getFullName();
                }
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'title' => ['label'=>'entities.lawyer.fields.description'],
                    'summary' => ['label'=>'entities.lawyer.fields.curriculum', 'attr'=>['class'=>'summernote']],
                    'content' => ['label'=>'entities.lawyer.fields.training', 'attr'=>['class'=>'summernote']],
                    'caption' => ['label'=>'entities.lawyer.fields.mentions', 'attr'=>['class'=>'summernote']],
                    'tags' => ['label'=>'entities.lawyer.fields.description', 'attr'=>['class'=>'summernote']],
                    'lawyer_tags' => ['label'=>'entities.lawyer.fields.curriculum', 'attr'=>['class'=>'summernote']],
                    'office_tags' => ['label'=>'entities.lawyer.fields.training', 'attr'=>['class'=>'summernote']],
                    'practice_tags' => ['label'=>'entities.lawyer.fields.mentions', 'attr'=>['class'=>'summernote']],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
            'translation_domain' => 'admin'
        ]);
    }
}
