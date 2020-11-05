<?php

namespace App\Form;

use App\Entity\Resource;
use App\Form\Type\UploaderFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;

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
                    'Thumbnail' => 'publication_thumbnail',
                    'Dossier' => 'publication_dossier',
                    'Foto Principal' => 'publication_main_photo'
                ],
                'required' => false,
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
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
