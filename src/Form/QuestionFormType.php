<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

use App\Entity\Question;

class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'question' => [
                        'label'=>'entities.question.fields.question',
                        'required'=>true,
                    ],
                    'hash' => [
                        'label'=>'entities.question.fields.hash',
                        'attr' => array(
                            'readonly' => true,
                        ),
                    ],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
