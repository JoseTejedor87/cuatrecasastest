<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\Lawyer;
use App\Entity\Speaker;


class SpeakerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=>'entities.speaker.fields.name'])
            ->add('surname', TextType::class, ['label'=>'entities.speaker.fields.surname'])
            ->add('position', TextType::class, ['label'=>'entities.speaker.fields.position'])
            ->add('lawyer', EntityType::class, [
                'class' => Lawyer::class,
                'label' => 'entities.speaker.fields.lawyer',
                'help' => 'entities.speaker.help.lawyer',
                'placeholder' => '',
                'attr' => [
                    'class' => 'm-select2'
                ],
                'choice_label' => function ($lawyer) {
                    return $lawyer->getFullName();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Speaker::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
