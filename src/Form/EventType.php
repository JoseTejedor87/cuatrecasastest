<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url_pdf',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('email',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('status',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('principal',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('image',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('url_video',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('url_inscription',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('contact',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('phone',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('program',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('date_notification',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('featured',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('type',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('visible',null, ['attr' => array('class' => 'form-control m-input')])
            ->add('translations', TranslationsType::class, [
                'fields' => [                               
                    'title' => ['attr' => array('class' => 'form-control m-input')],
                    'summary' => ['attr' => array('class' => 'form-control m-input')],
                    'city' => ['attr' => array('class' => 'form-control m-input')],
                    'place_description' => ['attr' => array('class' => 'form-control m-input')],
                ],
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
