<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Event;
use App\Entity\Person;
use App\Entity\Activity;
use App\Entity\Resource;
use App\Form\Type\EventCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;
use App\Form\ResourceFormType;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, ['label'=>'entities.event.fields.startDate', 'required' => true])
            ->add('endDate', DateType::class, ['label'=>'entities.event.fields.endDate', 'required' => true])
            ->add('eventType', EventCategoryType::class, ['label'=>'entities.event.fields.eventType', 'required' => true])
            ->add('contact', TextType::class, ['label'=>'entities.event.fields.contact'])
            ->add('phone', TextType::class, ['label'=>'entities.event.fields.phone'])
            ->add('capacity', IntegerType::class, ['label'=>'entities.event.fields.capacity'])
            ->add('customMap', TextType::class, ['label'=>'entities.event.fields.customMap'])
            ->add('customSignup', TextType::class, ['label'=>'entities.event.fields.customSignup'])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('attachments', CollectionType::class, [
                'label' => 'entities.event.fields.attachments',
                'entry_type' => ResourceFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.event.fields.activities',
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
            ->add('people', EntityType::class, [
                'class' => Person::class,
                'label' => 'entities.event.fields.people',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($person) {
                    return $person->getFullName();
                }
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.event.fields.title', 'required' => true],
                    'description' => ['label'=>'entities.event.fields.description', 'attr'=>['class'=>'summernote']],
                    'schedule' => ['label'=>'entities.event.fields.schedule', 'attr'=>['class'=>'summernote']],
                    'program' => ['label'=>'entities.event.fields.program', 'attr'=>['class'=>'summernote']],
                    'customCity' => ['label'=>'entities.event.fields.customCity'],
                    'customAddress' => ['label'=>'entities.event.fields.customAddress'],
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription']
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
