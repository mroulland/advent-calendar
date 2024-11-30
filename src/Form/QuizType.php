<?php

namespace App\Form;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class QuizType extends AbstractType
{
    private $assetPackages;

    public function __construct(Packages $assetPackages)
    {
        $this->assetPackages = $assetPackages;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Utilise l'option "questions" pour configurer les champs dynamiques
        foreach ($options['questions'] as $key => $question) {
            if(!empty($question["choices"])){
                $builder
                    ->add($key, ChoiceType::class, [
                        'attr' => [
                            'class' => 'choice-type'
                        ],
                        'label' => $question["question"],
                        'label_attr' => [
                            'class' => 'choice-type-label'
                        ],
                        'choices' => array_combine($question["choices"], $question["choices"]),  
                        'expanded' => true,
                        'multiple' => false,
                        'placeholder' => null
                    ]
                );
            }
            elseif(!empty($question["audio"])){
                $audioUrl = $this->assetPackages->getUrl('audio/'.$question["audio"]);

                $builder
                    ->add($key, TextType::class, [
                        
                        'attr' => [
                            'class' => 'audio-type text-type'
                        ],
                        'label' => $question["question"].'<audio controls>
                                        <source src="'.$audioUrl.'" type="audio/mpeg">
                                        Votre navigateur ne supporte pas l\'élément audio.
                                    </audio>',
                        'label_attr' => [
                            'class' => 'audio-type-label'
                        ],
                        'label_html' => true
                ]);
            }
            else{
                $builder
                    ->add($key, TextType::class, [
                        'attr' => [
                            'class' => 'text-type'
                        ],
                        'label' => $question["question"],
                    ]
                );
            }
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
