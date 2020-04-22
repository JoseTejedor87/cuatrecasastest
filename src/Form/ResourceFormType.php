<?php

namespace App\Form;

use App\Entity\Resource;
use App\Form\Type\UploaderFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Form\Type\LanguageType;

class ResourceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>'entities.resource.fields.title'
            ])
            ->add('file', UploaderFileType::class, [
                'label'=>'entities.resource.fields.file',
                'translation_domain' => 'admin',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'allow_extra_fields' => true
            ])            
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'article_thumbnail' => 'article_thumbnail',
                    'article_dossier' => 'article_dossier',
                    'article_main_photo' => 'article_main_photo'
                ],
                'required' => false,
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
