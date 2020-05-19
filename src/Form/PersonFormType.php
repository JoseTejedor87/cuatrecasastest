<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\Lawyer;
use App\Entity\Person;


class PersonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=>'entities.person.fields.name'])
            ->add('surname', TextType::class, ['label'=>'entities.person.fields.surname'])
            ->add('position', TextType::class, ['label'=>'entities.person.fields.position'])
            ->add('lawyer', EntityType::class, [
                'class' => Lawyer::class,
                'label' => 'entities.person.fields.lawyer',
                'help' => 'entities.person.help.lawyer',
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
            'data_class' => Person::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
