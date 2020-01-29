<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Mention;

class MentionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'body' => [
                        'label'=>'entities.mention.fields.body',
                        'required'=>true,
                        'attr'=>['class'=>'summernote']
                    ],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mention::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }

}
