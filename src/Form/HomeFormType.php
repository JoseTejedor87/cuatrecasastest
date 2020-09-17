<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Entity\Insight;
use App\Entity\Brand;
use App\Entity\Quote;
use App\Entity\Home;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\KnownLanguageType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;
use App\Form\ResourceFormType;


class HomeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('showSearchBlock', CheckboxType::class, ['required' => false,'label'=>'entities.home.fields.showSearchBlock'])
            ->add('showRelatedPublicationAndEvents', CheckboxType::class, ['required' => false,'label'=>'entities.home.fields.showRelatedPublicationAndEvents'])
            ->add('showInsight', CheckboxType::class, ['required' => false,'label'=>'entities.home.fields.showInsight'])
            ->add('showCarrerBlock', CheckboxType::class, ['required' => false,'label'=>'entities.home.fields.showCarrerBlock'])
            ->add('showQuoteBlock', CheckboxType::class, ['required' => false,'label'=>'entities.home.fields.showQuoteBlock'])
            ->add('insights', EntityType::class, [
                'class' => Insight::class,
                'label' => 'entities.home.fields.insight',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => false,
                'expanded' => false,
                'choice_label' => function ($insight) {
                    return $insight->translate('es')->getTitle();
                }
            ])
            ->add('quotes', EntityType::class, [
                'class' => Quote::class,
                'label' => 'entities.lawyer.fields.qoute',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choice_label' => function ($quote) {
                    return $quote->translate('es')->getBody();
                }
            ])
            ->add('brand', CollectionType::class, [
                'label'=>'entities.home.fields.brand',
                'entry_type' => BrandFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots']);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Home::class,
            'translation_domain' => 'admin'
        ]);
    }
}
