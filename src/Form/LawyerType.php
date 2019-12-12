<?php

namespace App\Form;

use App\Entity\Lawyer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LawyerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId', TextType::class, ['attr' => array('class' => 'form-control m-input')])
            // ->add('creation_date',null, ['attr' => array('class' => 'form-control m-input')])
            // ->add('update_date',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('role',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('name',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('surname',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('email',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('phone',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('fax',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('photo',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('lawyer_type',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('status',TextType::class, ['attr' => array('class' => 'form-control m-input')])
            ->add('translations', TranslationsType::class, [
                'fields' => [                               
                    'description' => ['attr' => array('class' => 'form-control m-input')],
                    'cv' => ['attr' => array('class' => 'form-control m-input')],
                    'experience' => ['attr' => array('class' => 'form-control m-input')],
                    'tags' => ['attr' => array('class' => 'form-control m-input')],
                    'training' => ['attr' => array('class' => 'form-control m-input')],
                    'mentions' => ['attr' => array('class' => 'form-control m-input')],
                ],
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lawyer::class,
        ]);
    }
}
