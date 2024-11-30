<?php

namespace App\Form\Admin;

use App\Entity\Challenge;
use App\Entity\QuizChallenge;
use App\Entity\PhotoChallenge;

use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\JsonArrayTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
        ->add('description', TextareaType::class, [
            'required' => false,
            'label' => 'Description',
            'attr' => [
                'class' => 'ckeditor',
                'minlength' => '10',
                'rows' => 10
            ],
        ]);

        
        // Ajout conditionnel du sous-formulaire
        if ($options['data'] instanceof QuizChallenge) {
            $builder->add('questions', TextareaType::class, [
                'required' => false,
                'label' => 'Questions'
            ]);

            $builder->get('questions')->addModelTransformer(new JsonArrayTransformer());
        }

        if ($options['data'] instanceof PhotoChallenge) {
            $builder->add('uploadDirectory', TextType::class, [
                'required' => false,
                'label' => 'Dossier où stocker les photos'
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class
        ]);
    }
}
