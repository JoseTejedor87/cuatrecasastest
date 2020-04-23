<?php

namespace App\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UploaderFileType extends VichFileType
{

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $object = $form->getParent()->getData();
        $view->vars['download_filename'] = $object ? $object->getFileName() : null;
        $view->vars['download_title'] = $object ? $object->getTitle() : null;
        $view->vars['allow_extra_fields'] = $object ? $object->getType() : null;
    }

}
