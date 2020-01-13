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
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class LawyerType extends AbstractType
{
    private $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=>'entities.lawyer.fields.name'])
            ->add('surname', TextType::class, ['label'=>'entities.lawyer.fields.surname'])
            ->add('email', EmailType::class, ['label'=>'entities.lawyer.fields.email'])
            ->add('phone', TextType::class, ['label'=>'entities.lawyer.fields.phone'])
            ->add('fax', TextType::class, ['label'=>'entities.lawyer.fields.fax'])
            ->add('photo', TextType::class, ['label'=>'entities.lawyer.fields.photo'])
            ->add('lawyerType', ChoiceType::class, [
                'label'=>'entities.lawyer.fields.lawyerType',
                'choices' => $this->params->get('app.lawyer_types'),
                'choice_label' => function ($choice, $key, $value) {
                    return 'entities.lawyer.lawyerTypes.'.$value;
                },
            ])
            // TODO: Create a centralized method to
            // obtain this collection.
            // from config parameters?
            ->add('languages', ChoiceType::class, [
                'label'=>'entities.lawyer.fields.languages',
                'expanded' => true,
                'choices' => $this->params->get('app.languages'),
                'choice_label' => function ($choice, $key, $value) {
                    return 'global.languages.'.$value;
                },
                'multiple' => true
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription'],
                    'description' => ['label'=>'entities.lawyer.fields.description'],
                    'curriculum' => ['label'=>'entities.lawyer.fields.curriculum'],
                    'training' => ['label'=>'entities.lawyer.fields.training'],
                ],
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lawyer::class,
            'translation_domain' => 'admin'
        ]);
    }
}
