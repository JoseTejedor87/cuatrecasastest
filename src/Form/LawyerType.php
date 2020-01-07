<?php

namespace App\Form;

use App\Entity\Lawyer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LawyerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, ['label'=>'admin.lawyers.fields.name'])
            ->add('surname',TextType::class, ['label'=>'admin.lawyers.fields.surname'])
            ->add('email',EmailType::class, ['label'=>'admin.lawyers.fields.email'])
            ->add('phone',TextType::class, ['label'=>'admin.lawyers.fields.phone'])
            ->add('fax',TextType::class, ['label'=>'admin.lawyers.fields.fax'])
            ->add('photo',TextType::class, ['label'=>'admin.lawyers.fields.photo'])
            ->add('lawyerType',ChoiceType::class, [
                'label'=>'admin.lawyers.fields.lawyer_type',
                'choices' => Lawyer::getLawyerTypes()
            ])
            // TODO: Create a centralized method to
            // obtain this collection.
            // from config parameters?
            ->add('languages',ChoiceType::class, [
                'label'=>'admin.lawyers.fields.languages',
                'expanded' => true,
                'choices'  => [
                    'Spanish' => 'es',
                    'English' => 'en',
                    'French' => 'fr',
                    'Chinese' => 'zh',
                ],
                'multiple' => true
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'admin.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'admin.publishable.fields.metaDescription'],
                    'description' => ['label'=>'admin.lawyers.fields.description'],
                    'curriculum' => ['label'=>'admin.lawyers.fields.curriculum'],
                    'training' => ['label'=>'admin.lawyers.fields.training'],
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
