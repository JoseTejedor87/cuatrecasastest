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

use App\Entity\Office;
use App\Form\ResourceFormType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;

class OfficeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class, ['label'=>'entities.office.fields.city'])
            ->add('address', TextType::class, ['label'=>'entities.office.fields.address'])
            ->add('cp', IntegerType::class, ['label'=>'entities.office.fields.cp'])
            ->add('country', TextType::class, ['label'=>'entities.office.fields.country'])
            ->add('contact', TextType::class, ['label'=>'entities.office.fields.contact'])
            ->add('email', TextType::class, ['label'=>'entities.office.fields.email'])
            ->add('fax', TextType::class, ['label'=>'entities.office.fields.fax'])
            ->add('phone', TextType::class, ['label'=>'entities.office.fields.phone'])
            ->add('img_map', TextType::class, ['label'=>'entities.office.fields.img_map'])
            ->add('link_google', TextType::class, ['label'=>'entities.office.fields.link_google'])
            ->add('status', TextType::class, ['label'=>'entities.office.fields.status'])
            ->add('place', TextType::class, ['label'=>'entities.office.fields.place'])
            ->add('geographical_area', TextType::class, ['label'=>'entities.office.fields.geographical_area'])
            ->add('sap', TextType::class, ['label'=>'entities.office.fields.sap'])
            ->add('img_office', ResourceFormType::class, [
                'label'=>'entities.office.fields.img_office'
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'descriptions' => ['label'=>'entities.office.fields.descriptions', 'attr'=>['class'=>'summernote']],
                    'tags' => ['label'=>'entities.office.fields.tags', 'attr'=>['class'=>'summernote']],
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
