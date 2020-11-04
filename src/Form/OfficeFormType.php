<?php

namespace App\Form;

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

class OfficeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class, ['label'=>'entities.office.fields.address'])
            ->add('cp', TextType::class, ['label'=>'entities.office.fields.cp'])
            ->add('contact', TextType::class, ['label'=>'entities.office.fields.contact'])
            ->add('email', TextType::class, ['label'=>'entities.office.fields.email'])
            ->add('fax', TextType::class, ['label'=>'entities.office.fields.fax'])
            ->add('phone', TextType::class, ['label'=>'entities.office.fields.phone'])
            ->add('status', IntegerType::class, ['label'=>'entities.office.fields.status'])
            ->add('place', OfficePlaceType::class, ['label'=>'entities.office.fields.place'])
            ->add('link_external_map', TextType::class, ['label'=>'entities.office.fields.link_google'])
            ->add('geographical_area', IntegerType::class, ['label'=>'entities.office.fields.geographical_area'])
            ->add('sap', TextType::class, ['label'=>'entities.office.fields.sap'])
            ->add('img_office', ResourceFormType::class, [
                'label'=>'entities.office.fields.img_office'
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('slug', TextType::class, ['required' => false,'label'=>'entities.lawyer.fields.slug'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'description' => ['label'=>'entities.office.fields.descriptions', 'attr'=>['class'=>'summernote'] ,'required' => true],
                    'tags' => ['label'=>'entities.office.fields.tags', 'attr'=>['class'=>'summernote'] ,'required' => false],
                    'city' => ['label'=>'entities.office.fields.city'],
                    'country' => ['label'=>'entities.office.fields.country'],

                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Office::class,
            'translation_domain' => 'admin'
        ]);
    }
}
