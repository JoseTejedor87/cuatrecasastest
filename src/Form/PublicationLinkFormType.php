<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


use App\Entity\PublicationLink;

class PublicationLinkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_time', DateTimeType::class, ['label'=>'entities.publicationLink.fields.date_time', 'required' => true])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => [
                        'label'=>'entities.programs.fields.title',
                        'required' => true
                    ],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PublicationLink::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
