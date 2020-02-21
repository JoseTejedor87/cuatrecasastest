<?php

namespace App\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Activity;
use App\Entity\Block;
use App\Entity\Quote;
use App\Entity\Event;
use App\Entity\QuoteBlock;
use App\Entity\EventsBlock;
use App\Form\Type\EventCategoryType;

class BlockFormType extends AbstractType implements DataMapperInterface
{
    const TRANSLATION_PREFIX = 'forms.choices.blockTypes';

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // BLOCK
            // *******************************************
            ->add('type', ChoiceType::class, [
                'label'=>false,
                'choices' => $this->params->get('app.block_types'),
                'choice_label' => function ($choice, $key, $value) {
                    return self::TRANSLATION_PREFIX . ".$value";
                },
            ])
            ->add('position', TextType::class, [
                'label'=>false
            ])
            // QUOTE BLOCK
            // *******************************************
            ->add('quote', EntityType::class, [
                'class' => Quote::class,
                'label' => 'entities.quoteBlock.fields.quote',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => false,
                'expanded' => false,
                'choice_label' => function ($quote) {
                    return $quote->translate('es')->getBody();
                }
            ])
            // EVENTS BLOCK
            // *******************************************
            ->add('eventType', EventCategoryType::class, [
                'label'=>'entities.eventsBlock.fields.eventType',
                'required'=>false
            ])
            ->add('numberOfEvents', IntegerType::class, [
                'label'=>'entities.eventsBlock.fields.numberOfEvents'
            ])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.eventsBlock.fields.activities',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            // Adding a DataMapper to the Form
            // the mapper will adapt the interchanged data between the form and the models
            // using the field "type"
            ->setDataMapper($this)
        ;
    }

    public function mapDataToForms($viewData, $forms)
    {
        // there is no data yet, so nothing to prepopulate
        if ($viewData === null) {
            return;
        }
        // invalid data type
        if (!$viewData instanceof Block) {
            throw new UnexpectedTypeException($viewData, Block::class);
        }
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $blockType = $viewData->getBlockType();

        // initialize form field values
        $forms['position']->setData($viewData->getPosition());
        $forms['type']->setData($blockType);

        if ($blockType == 'quoteBlock') {
            if ($quote = $viewData->getQuote()) {
                $forms['quote']->setData($quote);
            }
        }
        elseif ($blockType == 'eventsBlock') {
            $forms['numberOfEvents']->setData($viewData->getNumberOfEvents());
            $forms['eventType']->setData($viewData->getEventType());
            $forms['activities']->setData($viewData->getActivities());
        }
    }

    public function mapFormsToData($forms, &$viewData)
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $blockType = $forms['type']->getData();

        if ($blockType == 'quoteBlock') {
            $block = new QuoteBlock();
            $block->setQuote($forms['quote']->getData());
        }
        elseif ($blockType == 'eventsBlock') {
            $block = new EventsBlock();
            $block->setNumberOfEvents($forms['numberOfEvents']->getData());
            $block->setEventType($forms['eventType']->getData());
            foreach ($forms['activities']->getData() as $activity) {
                $block->addActivity($activity);
            }
        }
        $block->setPosition($forms['position']->getData());
        $viewData = $block;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin',
            'required' => false
        ]);
    }
}
