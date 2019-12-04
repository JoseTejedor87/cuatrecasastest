<?php

namespace App\Form;

use App\Entity\Lawyer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LawyerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId')
            ->add('creation_date')
            ->add('update_date')
            ->add('role')
            ->add('name')
            ->add('surname')
            ->add('email')
            ->add('phone')
            ->add('fax')
            ->add('photo')
            ->add('lawyer_type')
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lawyer::class,
        ]);
    }
}
