<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Program;
use App\Entity\Person;
use App\Entity\Event;


use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

// use App\Form\Type\;



class ProgramFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_time', DateTimeType::class, ['label'=>'entities.programs.fields.date_time', 'required' => true])
            ->add('people', EntityType::class, [
                'class' => Person::class,
                'label' => 'entities.programs.fields.people',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($person) {
                    return $person->getFullName();
                }
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => [
                        'label'=>'entities.programs.fields.title', 
                        'required' => true
                    ],
                    'description' => [
                        'label'=>'entities.programs.fields.description',
                        'required' => true, 
                        'attr'=>['class'=>'summernote']
                    ],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
    
}
