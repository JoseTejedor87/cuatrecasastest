<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

use App\Entity\Activity;
use App\Entity\Lawyer;
use App\Entity\Office;
use App\Entity\Mention;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\KnownLanguageType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\ResourceFormType;

class LawyerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true,'label'=>'entities.lawyer.fields.name'])
            ->add('surname', TextType::class, ['required' => true,'label'=>'entities.lawyer.fields.surname'])
            ->add('email', EmailType::class, ['required' => true,'label'=>'entities.lawyer.fields.email'])
            ->add('phone', TextType::class, ['required' => true,'help' => 'El telefono es texto','label'=>'entities.lawyer.fields.phone'])
            ->add('fax', TextType::class, ['required' => false,'label'=>'entities.lawyer.fields.fax'])
            ->add('slug', TextType::class, ['required' => false,'label'=>'entities.lawyer.fields.slug'])
            ->add('office', EntityType::class, [
                'class' => Office::class,
                'label' => 'entities.lawyer.fields.office',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => false,
                'required' => true,
                'expanded' => false,
                'choice_label' => function ($office) {
                    return $office->translate('es')->getCity();
                }
            ])
            ->add('photo', ResourceFormType::class, [
                'label'=>'entities.lawyer.fields.photo'
            ])
            ->add('lawyerType', LawyerCategoryType::class, ['label'=>'entities.lawyer.fields.lawyerType'])

            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.lawyer.fields.activities',
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

            ->add('secondaryActivities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.lawyer.fields.secondaryActivities',
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
            ->add('knownLanguages', KnownLanguageType::class, [
                'label'=>'entities.lawyer.fields.knownLanguages',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'description' => ['label'=>'entities.lawyer.fields.description', 'attr'=>['class'=>'summernote']],
                    'curriculum' => ['label'=>'entities.lawyer.fields.curriculum', 'attr'=>['class'=>'summernote']],
                    'training' => ['label'=>'entities.lawyer.fields.training', 'attr'=>['class'=>'summernote']],
                    'mentions' => ['label'=>'entities.lawyer.fields.mentions', 'attr'=>['class'=>'summernote']],
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
            'data_class' => Lawyer::class,
            'translation_domain' => 'admin'
        ]);
    }
}
