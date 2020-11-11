<?php

namespace App\Form;

use App\Entity\Award;
use App\Entity\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Office;
use App\Form\ResourceFormType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\OfficePlaceType;
use App\Form\Type\MetaRobotsType;

class RegionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('principal', CheckboxType::class, ['label'=>'entities.region.fields.principal','required' => false])
            ->add('office', EntityType::class, [
                'class' => Office::class,
                'label' => 'entities.region.fields.office',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choice_label' => function ($office) {
                    return $office->translate('es')->getCity();
                }
            ])

            ->add('photo', ResourceFormType::class, [
                'label'=>'entities.region.fields.photo'
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'title' => ['label'=>'entities.region.fields.title'],
                    'summary' => ['label'=>'entities.region.fields.summary', 'attr'=>['class'=>'summernote']],
                    'content' => ['label'=>'entities.region.fields.content', 'attr'=>['class'=>'summernote']],
                    'slug' => ['label'=>'entities.activity.fields.slug'],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Region::class,
            'translation_domain' => 'admin'
        ]);
    }
}
