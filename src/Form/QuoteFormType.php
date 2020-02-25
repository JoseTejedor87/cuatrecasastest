<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

use App\Entity\Quote;


class QuoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', TextType::class, ['label'=>'entities.quote.fields.author'])
            ->add('year', TextType::class, ['label'=>'entities.quote.fields.year'])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'body' => ['label'=>'entities.quote.fields.body', 'attr'=>['class'=>'summernote']],
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
