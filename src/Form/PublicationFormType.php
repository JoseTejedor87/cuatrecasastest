<?php

namespace App\Form;

use App\Entity\Insight;
use App\Entity\Legislation;
use App\Repository\OfficeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


use App\Entity\Publication;
use App\Entity\Activity;
use App\Entity\Office;
use App\Entity\Person;
use App\Entity\Page;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;
use App\Form\ResourceFormType;

class PublicationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('status', IntegerType::class, ['required' => true,'label'=>'entities.publication.fields.status'])
            ->add('featured', IntegerType::class, ['required' => true,'label'=>'entities.publication.fields.featured'])
            ->add('url_video', TextType::class, ['required' => false,'label'=>'entities.publication.fields.url_video'])
            ->add('publication_date', DateTimeType::class, ['label'=>'entities.publication.fields.publication_date', 'required' => true])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.publication.fields.activities',
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
                'label' => 'entities.publication.fields.offices',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'query_builder' => function (OfficeRepository $or){
                    return $or->createQueryBuilder('o')
                        ->join('o.translations', 'to')
                        ->andWhere('to.city is not null')
                        ->orderBy('to.city', 'ASC');
                }
//                'choice_label' => function ($office) {
//                    return $office->translate('es')->getCity();
//                }
            ])
            ->add('insights', EntityType::class, [
                'class' => Insight::class,
                'label' => 'entities.publication.fields.insights',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($insight) {
                    return $insight->translate('es')->getTitle();
                }
            ])
            ->add('legislations', EntityType::class, [
                'class' => Legislation::class,
                'label' => 'entities.publication.fields.legislations',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($legislations) {
                    return $legislations->getName();
                }
            ])
            ->add('people', EntityType::class, [
                'class' => Person::class,
                'label' => 'entities.publication.fields.people',
                'placeholder' => '',
                'attr' => [
                    'class' => 'm-select2'
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($person) {
                    return $person->getFullName();
                }
            ])
            ->add('pages', EntityType::class, [
                'class' => Page::class,
                'label' => 'entities.publication.fields.pages',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($page) {
                    return $page->translate('es')->getTitle();
                }
            ])
            ->add('attachments', CollectionType::class, [
                'label' => 'entities.event.fields.attachments',
                'entry_type' => ResourceFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'title' => ['label'=>'entities.publication.fields.title'],
                    'summary' => ['label'=>'entities.publication.fields.summary', 'attr'=>['class'=>'summernote'], 'required' => false],
                    'content' => ['label'=>'entities.publication.fields.content', 'attr'=>['class'=>'summernote'], 'required' => false],
                    /*
                    'caption' => ['label'=>'entities.publication.fields.caption', 'attr'=>['class'=>'summernote'], 'required' => false],
                    'tags' => ['label'=>'entities.publication.fields.tags', 'attr'=>['class'=>'summernote'], 'required' => false],
                    'lawyer_tags' => ['label'=>'entities.publication.fields.lawyer_tags', 'attr'=>['class'=>'summernote'], 'required' => false],
                    'office_tags' => ['label'=>'entities.publication.fields.office_tags', 'attr'=>['class'=>'summernote'], 'required' => false],
                    'practice_tags' => ['label'=>'entities.publication.fields.practice_tags', 'attr'=>['class'=>'summernote'], 'required' => false],
                    */
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
            'data_class' => Publication::class,
            'translation_domain' => 'admin'
        ]);
    }
}
