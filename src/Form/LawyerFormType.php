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
use App\Entity\Mention;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\LanguageType;
use App\Form\ResourceFormType;

class LawyerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=>'entities.lawyer.fields.name'])
            ->add('surname', TextType::class, ['label'=>'entities.lawyer.fields.surname'])
            ->add('email', EmailType::class, ['label'=>'entities.lawyer.fields.email'])
            ->add('phone', TextType::class, ['label'=>'entities.lawyer.fields.phone'])
            ->add('fax', TextType::class, ['label'=>'entities.lawyer.fields.fax'])
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
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            ->add('mentions', EntityType::class, [
                'class' => Mention::class,
                'label' => 'entities.lawyer.fields.mentions',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($mention) {
                    return $mention->translate('es')->getBody();
                }
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'description' => ['label'=>'entities.lawyer.fields.description', 'attr'=>['class'=>'summernote']],
                    'curriculum' => ['label'=>'entities.lawyer.fields.curriculum', 'attr'=>['class'=>'summernote']],
                    'training' => ['label'=>'entities.lawyer.fields.training', 'attr'=>['class'=>'summernote']],
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
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
