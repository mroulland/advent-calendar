<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Utilise l'option "questions" pour configurer les champs dynamiques
        foreach ($options['questions'] as $key => $question) {

            $builder
                ->add($key, TextType::class, [
                'label' => $question,
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // Si aucune entité n'est liée
        ]);
        
        // Déclare l'option "questions"
        $resolver->setDefined(['questions']);
        $resolver->setAllowedTypes('questions', 'array'); // S'assurer que c'est un tableau
    }

}
