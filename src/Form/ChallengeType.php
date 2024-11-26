<?php

namespace App\Form;

use App\Entity\Challenge;
use App\Form\JsonArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class ChallengeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('type', ChoiceType::class, [
            'choices' => [
                'Quiz' => 'quiz',
                'Photo' => 'photo',
            ],
            'mapped' => false,
            'label' => 'Type de défi'
        ])
        ->add('title', TextType::class, [
            'label' => 'Titre'
        ])
        ->add('description', TextType::class, [
            'required' => false,
            'label' => 'Description'
        ])
        ->add('questions', TextareaType::class, [
            'required' => false,
            'mapped' => false, // uniquement pour les quiz
            'label' => 'Questions'
        ])
        ->add('answers', TextareaType::class, [
            'required' => false,
            'mapped' => false, // uniquement pour les quiz
            'label' => 'Réponses'
        ])
        ->add('uploadDirectory', TextType::class, [
            'required' => false,
            'mapped' => false, // uniquement pour les photos
            'label' => 'Dossier où stocker les photos'
        ]);

        $builder->get('questions')->addModelTransformer(new JsonArrayTransformer());
        $builder->get('answers')->addModelTransformer(new JsonArrayTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class
        ]);
    }
}
