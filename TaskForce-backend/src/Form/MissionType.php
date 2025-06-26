<?php

namespace App\Form;

use App\Entity\Mission;
use App\Entity\Competence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
            ->add('competences', EntityType::class, [
                'class' => Competence::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Compétences requises',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
            'csrf_protection' => false,
        ]);
    }
}
