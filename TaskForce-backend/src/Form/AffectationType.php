<?php

namespace App\Form;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
                'choice_label' => 'titre', // plus lisible que 'id'
            ])
            ->add('collaborateur', EntityType::class, [
                'class' => Collaborateur::class,
                'choice_label' => 'prenom', // ou 'nomComplet' si défini
            ])
            ->add('role', TextType::class)
            ->add('date_affectation', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectation::class,
            'csrf_protection' => false,       // 🔒 Désactivé pour API
            'allow_extra_fields' => true,     // 🟡 Pour éviter les erreurs si JSON trop riche
        ]);
    }
}
