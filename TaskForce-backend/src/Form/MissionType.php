<?php

namespace App\Form;

use App\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nom')
            ->add('statut')
            ->add('competencesRequises', TextType::class, [
                'required' => false,
                'label' => 'Compétences requises (séparées par des virgules)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
            'csrf_protection' => false, // 🔒 désactivation pour l'API

        ]);
    }
}
