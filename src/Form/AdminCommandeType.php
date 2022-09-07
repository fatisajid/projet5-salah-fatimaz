<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Chambre;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AdminCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateDepart', DateTimeType::class, [
            'widget' => 'single_text',
            'attr' => [
                'min' => (new \DateTime())->format('Y-m-d H:i'),
            ]
        ])
        ->add('dateArrivee', DateTimeType::class, [
            'widget' => 'single_text'
        ])
        ->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'pseudo'
        ])
        ->add('chambre', EntityType::class, [
            'class' => Chambre::class,
            'choice_label' => 'titre'
        ])

        ->add('telephone')
        ->add('email')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
