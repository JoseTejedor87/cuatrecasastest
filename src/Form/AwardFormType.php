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

use App\Entity\Award;
use App\Form\ResourceFormType;

class AwardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', IntegerType::class, ['required' => true,'label'=>'entities.award.fields.status'])
            ->add('image', ResourceFormType::class, [
                'label'=>'entities.award.fields.image'
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'title' => ['label'=>'entities.award.fields.title'],
                    'granted' => ['label'=>'entities.award.fields.granted', 'attr'=>['class'=>'summernote']],
                    'desc_award' => ['label'=>'entities.award.fields.desc_award', 'attr'=>['class'=>'summernote']],
                    'desc_award_firma' => ['label'=>'entities.award.fields.desc_award_firma', 'attr'=>['class'=>'summernote']],
                    'desc_award_indiv' => ['label'=>'entities.award.fields.desc_award_indiv', 'attr'=>['class'=>'summernote']],
                    'tags' => ['label'=>'entities.award.fields.tags', 'attr'=>['class'=>'summernote']],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Award::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
