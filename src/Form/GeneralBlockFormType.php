<?php

namespace App\Form;

use App\Entity\GeneralBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;

class GeneralBlockFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.generalBlock.fields.title', 'required'=>true],
                    'subtitle' => ['label'=>'entities.generalBlock.fields.subtitle', 'required'=>false],
                    'content' => ['label'=>'entities.generalBlock.fields.content', 'attr'=>['class'=>'summernote'], 'required'=>false],
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'slug' => ['label'=>'Slug']
                ],
            ])
            ->add('blockName', TextType::class, ['label'=>'entities.generalBlock.fields.name', 'required' => true ])
            ->add('customTemplate',  TextType::class, ['label'=>'Custom-Template', 'required' => true])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published'])

            ->add('blocks', CollectionType::class, [
                'label'=>'entities.generalBlock.fields.blocks',
                'entry_type' => BlockFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GeneralBlock::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
