<?php

namespace App\Form;

use App\Entity\HangmanChallenge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HangmanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('word', TextType::class, [
                'mapped' => false,
                'label' => 'Mot à deviner'
            ])
            ->add('attempts', IntegerType::class, [
                'label' => 'Nombre de tentatives',
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HangmanChallenge::class,
        ]);
    }
}
