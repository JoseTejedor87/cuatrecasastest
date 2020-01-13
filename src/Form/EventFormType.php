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
use App\Entity\Event;
use App\Form\Type\EventCategoryType;
use App\Form\Type\LanguageType;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, ['label'=>'entities.event.fields.startDate'])
            ->add('endDate', DateType::class, ['label'=>'entities.event.fields.endDate'])
            ->add('eventType', EventCategoryType::class, ['label'=>'entities.event.fields.eventType'])
            ->add('contact', TextareaType::class, ['label'=>'entities.event.fields.contact', 'attr'=>['class'=>'summernote']])
            ->add('capacity', IntegerType::class, ['label'=>'entities.event.fields.capacity'])
            ->add('customMap', TextType::class, ['label'=>'entities.event.fields.customMap'])
            ->add('customSignup', TextType::class, ['label'=>'entities.event.fields.customSignup'])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.event.fields.title'],
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
            'translation_domain' => 'admin'
        ]);
    }
}
