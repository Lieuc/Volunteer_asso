<?php

namespace App\Form;

use App\Entity\Domain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class MissionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchTerm', SearchType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une mission (nom, description)',
                    'class' => 'rounded-xl px-8 py-4 bg-gray-100 w-full',
                ],
            ])
            ->add('domain', EntityType::class, [
                'class' => Domain::class,
                'choice_label' => 'name',
                'label' => false,
                'required' => false,
                'placeholder' => 'Tous les domaines',
                'attr' => [
                    'class' => 'rounded-xl px-8 py-4 bg-gray-100',
                ],

            ]);
    }
}
