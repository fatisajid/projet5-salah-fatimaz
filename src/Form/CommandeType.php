<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CommandeType extends AbstractType
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
        ->add('telephone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
