<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Nom de l’association']])
            ->add('description', TextareaType::class, ['label' => false, 'required' => false, 'attr' => ['placeholder' => 'Description']])
            ->add('rnaNumber', TextType::class, ['label' => false, 'required' => false, 'attr' => ['placeholder' => 'Numéro RNA']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Association::class]);
    }
}
