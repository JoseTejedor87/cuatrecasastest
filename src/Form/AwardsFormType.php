<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Awards;
use App\Form\ResourceFormType;

class AwardsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', IntegerType::class, ['required' => true,'label'=>'entities.awards.fields.status'])
            ->add('img_office', ResourceFormType::class, [
                'label'=>'entities.awards.fields.img_office'
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'title' => ['label'=>'entities.awards.fields.title'],
                    'granted' => ['label'=>'entities.awards.fields.granted', 'attr'=>['class'=>'summernote']],
                    'desc_award' => ['label'=>'entities.awards.fields.desc_award', 'attr'=>['class'=>'summernote']],
                    'desc_award_firma' => ['label'=>'entities.awards.fields.desc_award_firma', 'attr'=>['class'=>'summernote']],
                    'desc_award_indiv' => ['label'=>'entities.awards.fields.desc_award_indiv', 'attr'=>['class'=>'summernote']],
                    'tags' => ['label'=>'entities.awards.fields.tags', 'attr'=>['class'=>'summernote']],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Awards::class,
            'translation_domain' => 'admin'
        ]);
    }
}
