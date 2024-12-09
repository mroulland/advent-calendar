<?php

namespace App\Form\Admin;

use App\Entity\Calendar;
use App\Entity\Challenge;
use App\Repository\ChallengeRepository;
use Doctrine\ORM\EntityRepository;
use PhpParser\Node\Expr\Cast;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('challenge', EntityType::class, [
                'class' => Challenge::class,
                'placeholder' => 'Choisir un challenge',
                'query_builder' => function(ChallengeRepository $repository){
                    return $repository->createQueryBuilder('a')
                            ->leftJoin(Calendar::class, 'b', 'WITH','b.challenge = a')
                            ->where('b.id IS NULL');
                },
                'required' => false,
                'choice_label' => 'title',
            ])
            ->add('picture', TextType::class, [
                'label' => 'Image (nom)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
