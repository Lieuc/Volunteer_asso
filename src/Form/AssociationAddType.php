<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AssociationAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('rnaNumber', TextType::class, [
                'label' => 'Numéro RNA',
                'required' => false,
            ])
            ->add('url', TextType::class, [
                'label' => 'Url du site web',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [   // ✅ champ non mappé
                'label' => 'Image',
                'mapped' => false,   // pas lié directement à l’entité
                'required' => false,
            ]);
    }
}
