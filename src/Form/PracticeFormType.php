<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Activity;
use App\Entity\Practice;
use App\Form\ActivityFormType;

class PracticeFormType extends ActivityFormType
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
                'class' => Practice::class,
                'label' => 'entities.practice.fields.children',
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
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Practice::class,
            'required' => false
        ]);
    }
}
