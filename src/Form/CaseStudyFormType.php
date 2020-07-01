<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;
use App\Form\ResourceFormType;
use App\Entity\Activity;
use App\Entity\Event;
use App\Entity\Lawyer;
use App\Entity\CaseStudy;
use App\Entity\Quote;

class CaseStudyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.case_study.fields.title', 'required'=>true],
                    'slug' => ['label'=>'entities.case_study.fields.slug', 'required'=>false],
                    'summary' => ['label'=>'entities.case_study.fields.summary', 'attr'=>['class'=>'summernote']],
                    'description' => ['label'=>'entities.case_study.fields.description', 'attr'=>['class'=>'summernote']],
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription']
                ],
            ])
            ->add('lawyers', EntityType::class, [
                'class' => Lawyer::class,
                'label' => 'entities.case_study.fields.lawyers',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choice_label' => function ($lawyer) {
                    return $lawyer->getFullName();
                }
            ])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.case_study.fields.activities',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            ->add('relatedCaseStudies', EntityType::class, [
                'class' => CaseStudy::class,
                'label' => 'entities.case_study.fields.relatedCaseStudies',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choice_label' => function ($caseStudy) {
                    return $caseStudy->translate('es')->getTitle();
                }
            ])
            ->add('quote', EntityType::class, [
                'class' => Quote::class,
                'label' => 'entities.quoteBlock.fields.quote',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($quote) {
                    return $quote->translate('es')->getBody();
                }
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('image', ResourceFormType::class, ['label'=>'entities.case_study.fields.image']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin'
        ]);
    }
}
