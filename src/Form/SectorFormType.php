<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Activity;
use App\Entity\Sector;
use App\Form\ActivityFormType;

class SectorFormType extends ActivityFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('relatedActivities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.activity.fields.relatedActivities',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            ->add('children', EntityType::class, [
                'class' => Sector::class,
                'label' => 'entities.sector.fields.children',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sector::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
