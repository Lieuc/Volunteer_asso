<?php

namespace App\Form;

use App\Entity\Domain;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la mission',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la mission',
                'required' => false,
            ])
            ->add('volunteerNeeded', IntegerType::class, [
                'label' => 'Nombre de bénévoles nécessaires',
                'required' => true,
            ])
            ->add('startAt', DateType::class, [
                'label' => 'Date de début',
                'required' => true,
                'widget' => 'single_text', // plus pratique avec un datepicker
            ])
            ->add('endAt', DateType::class, [
                'label' => 'Date de fin',
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('domains', EntityType::class, [
                'class' => Domain::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,  // coche = checkboxes
                'label' => 'Domaines liés à la mission',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class, // ⚠️ avant c’était Post
        ]);
    }
}
