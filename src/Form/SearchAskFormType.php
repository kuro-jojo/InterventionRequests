<?php

namespace App\Form;

use App\Entity\Pole;
use App\Entity\SearchAsk;
use App\DBAL\Types\CauseDefaillanceType;
use App\DBAL\Types\Priorite;
use App\DBAL\Types\StatutType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchAskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('statutDemande', ChoiceType::class, [
                'choices' => StatutType::getChoices(),
                'placeholder' => 'Statut',
            ])
            ->add(
                'prioriteDemande',
                ChoiceType::class,
                [
                    'choices' =>
                    Priorite::getChoices(),
                    'placeholder' => 'Priorité',
                ]
            )
            ->add('typeDefaillance', EntityType::class, [
                'placeholder' => 'Type de défaillance',
                'class' => Pole::class,
                'choice_label' => 'nomPole',
                'multiple' => false,
                'attr' => [
                    'class' => ''
                ],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchAsk::class,
            'attr' =>[
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
