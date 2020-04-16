<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Vich\UploaderBundle\Form\Type\VichImageType;


use App\Entity\Articles;
use App\Entity\Activity;
use App\Entity\Lawyer;
use App\Entity\Office;
use App\Form\Type\LanguageType;
use App\Form\ResourceFormType;

class ArticlesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', IntegerType::class, ['required' => true,'label'=>'entities.article.fields.status'])
            ->add('featured', IntegerType::class, ['required' => true,'label'=>'entities.article.fields.featured'])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.article.fields.activities',
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
                'label' => 'entities.article.fields.office',
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
            // ->add('lawyers', EntityType::class, [
            //     'class' => Lawyer::class,
            //     'label' => 'entities.article.fields.lawyers',
            //     'help' => 'entities.speaker.help.lawyer',
            //     'placeholder' => '',
            //     'attr' => [
            //         'class' => 'm-select2'
            //     ],
            //     'multiple' => true,
            //     'required' => true,
            //     'expanded' => false,
            //     'choice_label' => function ($lawyer) {
            //         return $lawyer->getFullName();
            //     }
            // ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'title' => ['label'=>'entities.article.fields.title'],
                    'summary' => ['label'=>'entities.article.fields.summary', 'attr'=>['class'=>'summernote']],
                    'content' => ['label'=>'entities.article.fields.content', 'attr'=>['class'=>'summernote']],
                    'caption' => ['label'=>'entities.article.fields.caption', 'attr'=>['class'=>'summernote']],
                    'tags' => ['label'=>'entities.article.fields.tags', 'attr'=>['class'=>'summernote']],
                    'lawyer_tags' => ['label'=>'entities.article.fields.lawyer_tags', 'attr'=>['class'=>'summernote']],
                    'office_tags' => ['label'=>'entities.article.fields.office_tags', 'attr'=>['class'=>'summernote']],
                    'practice_tags' => ['label'=>'entities.article.fields.practice_tags', 'attr'=>['class'=>'summernote']],
                ],
            ]);
    }
    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
            'translation_domain' => 'admin'
        ]);
    }
}
