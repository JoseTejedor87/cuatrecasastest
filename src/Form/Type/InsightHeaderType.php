<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class InsightHeaderType extends AbstractType
{
    private $params;
    const TRANSLATION_PREFIX = 'forms.choices.insightHeaderTypes';

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->params->get('app.insight_header_types'),
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
