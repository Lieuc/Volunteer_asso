<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ApplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //juste pour le csrf
    }
}
