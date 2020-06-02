<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class KnownLanguageType extends AbstractType
{
    private $params;
    const TRANSLATION_PREFIX = 'global.languages';

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => true,
            'choices' => $this->params->get('app.known_languages_by_lawyers'),
            'choice_label' => function ($choice, $key, $value) {
                return self::TRANSLATION_PREFIX . ".$value";
            },
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
