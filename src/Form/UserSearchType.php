<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class UserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchTerm', SearchType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un utilisateur (email, prénom, nom)',
                    'class' => 'rounded-xl px-8 py-4 bg-gray-100 w-full '
                ]
            ]);
    }
}

