<?php

namespace App\Form;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ParticipationType extends AbstractType
{
    private $assetPackages;

    public function __construct(Packages $assetPackages)
    {
        $this->assetPackages = $assetPackages;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Utilise l'option "questions" pour configurer les champs dynamiques
        foreach ($options['questions']['questions'] as $content) {

            $type = $content["type"] == 'textarea' ? TextareaType::class : TextType::class;
            
            $builder
                ->add($content["name"], $type, [
                    'attr' => [
                        'class' => 'text-type'
                    ],
                    'label' => $content['label'],
                ]
            );
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
