<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;
use App\Entity\Activity;
use App\Entity\Lawyer;
use App\Entity\Quote;
use App\Entity\Award;
use App\Form\ResourceFormType;
use App\Repository\LawyerRepository;

abstract class ActivityFormType extends AbstractType
{
    private $id_act;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->id_act = $options['data']->getId();
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.activity.fields.title', 'attr'=>[ 'required'=>true,'class'=>'required'], 'empty_data' => ' '],
                    'slug' => ['label'=>'entities.activity.fields.slug','attr'=>[ 'required'=>false], 'empty_data' => ' '],
                    'summary' => ['label'=>'entities.activity.fields.summary', 'attr'=>['class'=>'summernote', 'required'=>false]],
                    'description' => ['label'=>'entities.activity.fields.description', 'attr'=>['class'=>'summernote','required'=>false] , 'empty_data' => ' ' ],
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription']
                ],
            ])
            ->add('relatedActivities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.activity.fields.relatedActivities',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            ->add('key_contacts', EntityType::class, [
                'class' => Lawyer::class,
                'label' => 'entities.activity.fields.key_contacts',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => false,
                'expanded' => false,
                'query_builder' => function (LawyerRepository $lr) {
                    return $lr->createQueryBuilder('l')
                                    ->join('l.activities', 'a')
                                    ->join('l.secondaryActivities', 'sa')
                                    ->where('a.id = :id_act')
                                    ->orWhere('sa.id = :id_act')
                                    ->setParameter('id_act', $this->id_act)
                                    ->orderBy('l.name', 'ASC');
                }
            ])
            ->add('quote', EntityType::class, [
                'class' => Quote::class,
                'label' => 'entities.quoteBlock.fields.quote',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => false,
                'expanded' => false,
                'choice_label' => function ($quote) {
                    return $quote->translate('es')->getBody();
                }
            ])
            ->add('highlighted', CheckboxType::class, [
                'label'=>'entities.activity.fields.highlighted',
                'required'=>false])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published','required'=>false])
            ->add('photo', ResourceFormType::class, [
                'required' => false,
                'label'=>'entities.activity.fields.image'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin'
        ]);
    }
}
