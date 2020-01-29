<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Entity\Lawyer;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\LanguageType;


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
            ->add('imageFile', VichImageType::class, [
                'label'=>'entities.lawyer.fields.photo',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
            ])
            ->add('photo', TextType::class, ['label'=>'entities.lawyer.fields.photo'])
            ->add('lawyerType', LawyerCategoryType::class, ['label'=>'entities.lawyer.fields.lawyerType'])
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
