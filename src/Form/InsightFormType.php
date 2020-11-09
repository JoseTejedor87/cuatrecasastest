<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Entity\Activity;
use App\Entity\Lawyer;
use App\Entity\Insight;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;
use App\Form\Type\InsightHeaderType;

class InsightFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.insight.fields.activities',
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

            ->add('lawyers', EntityType::class, [
                'class' => Lawyer::class,
                'label' => 'entities.insight.fields.lawyers',
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

            ->add('relatedInsights', EntityType::class, [
                'class' => Insight::class,
                'label' => 'entities.insight.fields.relatedInsights',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choice_label' => function ($insight) {
                    return $insight->translate('es')->getTitle();
                }
            ])

            ->add('headerType', InsightHeaderType::class, ['label'=>'entities.insight.fields.headerType'])
            ->add('showIntroBlock', CheckboxType::class, ['label'=>'entities.insight.fields.showIntroBlock', 'required' => false])
            ->add('showKnowledgeBlock', CheckboxType::class, ['label'=>'entities.insight.fields.showKnowledgeBlock', 'required' => false])
            ->add('showEventsBlock', CheckboxType::class, ['label'=>'entities.insight.fields.showEventsBlock', 'required' => false])
            ->add('showLegalNoveltiesBlock', CheckboxType::class, ['label'=>'entities.insight.fields.showLegalNoveltiesBlock', 'required' => false])
            ->add('showCaseStudiesBlock', CheckboxType::class, ['label'=>'entities.insight.fields.showCaseStudiesBlock', 'required' => false])

            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.insight.fields.title'],
                    'summary' => ['label'=>'entities.insight.fields.summary', 'attr'=>['class'=>'summernote']],
                    'description' => ['label'=>'entities.insight.fields.description', 'attr'=>['class'=>'summernote']],
                    'slug' => ['label'=>'entities.insight.fields.slug', 'required' => false],
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                ],
            ])
            ->add('attachments', CollectionType::class, [
                'label' => 'entities.event.fields.attachments',
                'entry_type' => ResourceFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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
            'data_class' => Insight::class,
            'translation_domain' => 'admin'
        ]);
    }
}
