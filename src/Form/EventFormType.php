<?php

namespace App\Form;

use App\Entity\Insight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use App\Entity\Event;
use App\Entity\Person;
use App\Entity\Office;
use App\Entity\Activity;
use App\Entity\Resource;
use App\Form\Type\EventCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;
use App\Form\Type\MetaRobotsType;
use App\Form\ResourceFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\PersonRepository;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // IMPORTANTE CAMPOS OBLIGATORIOS PASADOS POR CLIENTE
        // Campos obligatorios: solo deben ser obligatorios los siguientes campos: "Fecha Inicio", "Fecha Final", "Hora Inicio", "Hora Final" y "Título del evento"  , id_evento_web, idTIpoWeb, tiponombre, urlics(Hay que implementarlo), urlweb
        // Null: aforo, areas, ciudad, contacto, id estado web si publicado o no publicado, id_oficina, oficina nombre, optional adress, Ponentes externos, ponentes internos, preguntas eventos, responsables marketing, secretarias , socios , urlimagenemail
        //URLWEB: Solo slug, tiene q estar la url completa
        $builder
            ->add('startDate', DateTimeType::class, ['label'=>'entities.event.fields.startDate', 'required' => true])
            ->add('endDate', DateTimeType::class, ['label'=>'entities.event.fields.endDate', 'required' => true])
            ->add('eventType', EventCategoryType::class, ['label'=>'entities.event.fields.eventType', 'required' => true])
            ->add('contact', TextType::class, ['label'=>'entities.event.fields.contact'])
            ->add('phone', TextType::class, ['label'=>'entities.event.fields.phone'])
            ->add('email', TextType::class, ['label'=>'entities.event.fields.email'])
            ->add('capacity', IntegerType::class, ['label'=>'entities.event.fields.capacity'])
            ->add('customMap', TextType::class, ['label'=>'entities.event.fields.customMap'])
            ->add('customSignup', TextType::class, ['label'=>'entities.event.fields.customSignup'])
            ->add('languages', LanguageType::class, ['label'=>'entities.publishable.fields.languages'])
            ->add('regions', RegionType::class, ['label'=>'entities.publishable.fields.regions'])
            ->add('metaRobots', MetaRobotsType::class, ['label'=>'entities.publishable.fields.metaRobots'])
            ->add('published', CheckboxType::class, ['label'=>'entities.publishable.fields.published'])
            ->add('featured', CheckboxType::class, ['label'=>'entities.event.fields.featured'])
            ->add('attachments', CollectionType::class, [
                'label' => 'entities.event.fields.attachments',
                'entry_type' => ResourceFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'label' => 'entities.event.fields.activities',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => false,
                'expanded' => false,
                'choice_label' => function ($activity) {
                    return $activity->translate('es')->getTitle();
                }
            ])
            ->add('insights', EntityType::class, [
                'class' => Insight::class,
                'label' => 'entities.event.fields.insights',
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
            ->add('people', EntityType::class, [
                'class' => Person::class,
                'label' => 'entities.event.fields.people',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'required' => false,
                'expanded' => false,
                'query_builder' => function (PersonRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->where("p.type is null")
                        ->orderBy('p.type', 'ASC');
                },
                'choice_label' => 'fullname',
            ])
            ->add('office', EntityType::class, [
                'class' => Office::class,
                'label' => 'entities.event.fields.office',
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'required' => false,
                'placeholder' => 'entities.event.fields.no-office',
                'empty_data' => null,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => function ($office) {
                    return $office->translate('es')->getCity();
                }
            ])

            ->add('responsablesmarketing', ChoiceType::class, [
                'label' => 'Responsables de marketing Sap',
                'required' => true,
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'mapped'=> false,
                'required' => false,
                'choices' =>  $this->getResponsablesMarketing($options['entityManager']),
                'data' =>  $this->getResponsablesSelected($options['entityManager'], $options['data'], 'marketing')
            ])
            ->add('secretarias', ChoiceType::class, [
                'label' => 'Secretarias Sap',
                'required' => true,
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'mapped'=> false,
                'required' => false,
                'choices' =>  $this->getSecretarias($options['entityManager']),
                'data' =>  $this->getResponsablesSelected($options['entityManager'], $options['data'], 'secretaria')
            ])
            ->add('sociosresponsables', ChoiceType::class, [
                'label' => 'Socios Responsables Sap',
                'required' => true,
                'attr' => [
                    'class' => 'm-select2',
                    'data-allow-clear' => true
                ],
                'multiple' => true,
                'mapped'=> false,
                'required' => false,
                'choices' =>  $this->getSociosResponsables($options['entityManager']),
                'data' =>  $this->getResponsablesSelected($options['entityManager'], $options['data'], 'socio')
            ])
            ->add('programs', CollectionType::class, [
                'label'=>'entities.event.fields.programs',
                'entry_type' => ProgramFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'by_reference' => false,
            ])
            ->add('questions', CollectionType::class, [
                'label'=>'entities.event.fields.questions',
                'entry_type' => QuestionFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'by_reference' => false,
            ])
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'title' => ['label'=>'entities.event.fields.title', 'required' => true],
                    'description' => ['label'=>'entities.event.fields.description', 'attr'=>['class'=>'summernote']],
                    // 'schedule' => ['label'=>'entities.event.fields.schedule', 'attr'=>['class'=>'summernote']],
                    'customCity' => ['label'=>'entities.event.fields.customCity'],
                    'customCountry' => ['label'=>'entities.event.fields.customCountry'],
                    'customPostalcode' => ['label'=>'entities.event.fields.customPostalcode'],
                    'customProvince' => ['label'=>'entities.event.fields.customProvince'],
                    'customAddress' => ['label'=>'entities.event.fields.customAddress'],
                    'metaTitle' => ['label'=>'entities.publishable.fields.metaTitle'],
                    'metaDescription' => ['label'=>'entities.publishable.fields.metaDescription']
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'translation_domain' => 'admin',
            'required' => false,
            'entityManager' => null,
        ]);
    }

    private function getResponsablesMarketing($em)
    {
        $conn = $em->getConnection();
        $sql = "SELECT * FROM GC_responsablesMarketings";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();
        $ValuesA = array();
        foreach ($ValuesO as $key => $bu) {
            $ValuesA[$bu['Nombre'].' '.$bu['Apellidos']] = $bu['Iniciales'];
        }
        return $ValuesA;
    }
    private function getResponsablesSelected($em, $event, $type)
    {
        $personRepository = $em->getRepository(Person::class);
        $person = $personRepository->findBy(['type' => $type ]);
        $ValuesA = array();
        foreach ($person as $key => $bu) {
            foreach ($bu->getEvents() as $key => $value) {
                if ($value->getId() == $event->getId()) {
                    array_push($ValuesA, $bu->getInicial());
                }
            }
        }

        return $ValuesA;
    }
    private function getSecretarias($em)
    {
        $conn = $em->getConnection();
        $sql = "SELECT * FROM GC_secretarias";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();
        $ValuesA = array();
        foreach ($ValuesO as $key => $bu) {
            $ValuesA[$bu['Nombre'].' '.$bu['Apellidos']] = $bu['Iniciales'];
        }
        return $ValuesA;
    }

    private function getSociosResponsables($em)
    {
        $conn = $em->getConnection();
        $sql = "SELECT * FROM GC_sociosResponsables";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();
        $ValuesA = array();
        foreach ($ValuesO as $key => $bu) {
            $ValuesA[$bu['Nombre'].' '.$bu['Apellidos']] = $bu['Iniciales'];
        }
        return $ValuesA;
    }
}
