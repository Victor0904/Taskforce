<?php

namespace App\Form;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role')
            ->add('date_affectation')
            ->add('collaborateur', EntityType::class, [
                'class' => Collaborateur::class,
'choice_label' => 'id',
            ])
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectation::class,
        ]);
    }
}
